<?php

use Enstar\Library\SpeedScore;
use Enstar\Library\Weixin\WeixinClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use J20\Uuid\Uuid;
//use RedisLog;
// use Reading;

class ReadQueueListen extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'xy-read:queue-listen';

	private $mqClient;
	private $wx;

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Start listening the queue from Python ASR service';

	/**
	 * Create a new command instance.
	 */
	public function __construct()
	{
		$this->mqClient = new ReadMQ();
		$this->wx = new WeixinClient();
		parent::__construct();
	}


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$key = Config::get('app.enstar_read_out_key');
		$redis = Redis::connection();

		if (!$redis) {
			return false;
		}

		while (true) {
			try {
				$reportStr = $redis->rpop($key);
			}
			catch(\Exception $e) {
				echo date('Y-m-d H:i:s') . '; redis error.';
				continue;
			}

			if (!empty($reportStr)) {
				//echo date('Y-m-d H:i:s') . '; get a msg:' . $reportStr . PHP_EOL;
				$report = json_decode($reportStr, true);

				if (!$report ||
					!isset($report['readId']) ||
					!isset($report['status'])) {
					echo date('Y-m-d H:i:s') . '; get an invalid msg: ' . $reportStr . PHP_EOL;
					continue;
				}

				try {
					$reading = Reading::find($report['readId']);
				} catch (\Exception $e) {
					DB::reconnect('mysql');
					echo date('Y-m-d H:i:s') . '; DB reconnect.' . PHP_EOL;
					$reading = Reading::find($report['readId']);
				}

				if (!$reading) {
					continue;
				}

				// handle report
				$reportPath = '';
				$status = 0;
				$score = 0;
				$pronunciationScore = 0;
				$intonationScore = 0;
				$stressScore = 0;
				$fluencyScore = 0;
				$speedScore = 0;
				$speedSituation = '';
				$speed = 0;
				$asr_time = 0;
				$completeness = 0;
				$duration = 0;

				if ($report['status'] == 'SUCCESS') {
					$reportPath = $report['reportPath'];
					$status = 100;
					$finalScore = $this->calculateScore($reportPath, $report['lesson_id']);
					if (!$finalScore) {
						continue;
					}
					$score = $finalScore['overallScore'];
					$completeness = $finalScore['completeness'];
					$pronunciationScore = $finalScore['pronunciationScore'];
					$intonationScore = $finalScore['intonationScore'];
					$stressScore = $finalScore['stressScore'];
					$fluencyScore = $finalScore['fluencyScore'];
					$speedScore = $finalScore['speedScore'];
					$speedSituation = $finalScore['speedSituation'];
					$speed = $finalScore['speed'];
					$duration = $finalScore['duration'];
					$asr_time = round($report['asr_time']);

					// save to advisory
					$objAdvisory = Advisory::where('reading_id', $report['readId'])->first();
					if (!$objAdvisory) { // 还没有该read_id的报告记录, insert
						$advisory = new Advisory();
						$advisory->guid = Uuid::v4();
						$advisory->reading_id = $report['readId'];
						$advisory->source = 1; // ASR
						$advisory->path = $reportPath;
						$advisory->save();
					} else { // 已有该read_id的报告记录，update
						$objAdvisory->guid = Uuid::v4();
						$objAdvisory->source = 1; // ASR
						$objAdvisory->path = $reportPath;
						$objAdvisory->save();
					}

					// push notification
					$msg = array(
						'touser'=>$reading->user->openid,
						'template_id'=>Config::get('weixin.template_id'),
						'url'=>route('readingDetail', array('reading_uuid' => $reading->uuid)),
						'topcolor'=>'#FF0000',
						'data'=>array(
							'first'=>array('value'=>'恭喜你评分成功！','color'=>'#173177'),
							'keyword1'=>array('value'=>$reading->lesson->title,'color'=>'#173177'),
							'keyword2'=>array('value'=>'朗读评分','color'=>'#173177'),
							'remark'=>array('value'=>'完成度：'.round($completeness,0).'%'.'，得分：'.round($score, 0),'color'=>'#173177'),
						),
					);
					//通知微信评分完成
					$this->wx->sendTemplateMessage(json_encode($msg), $this->mqClient->getWeixinAccessToken());
				} elseif ($report['status'] == 'PROCESSING') {
					$status = 10;
				} else { // FAIL
					$status = -1;
				}

				// update reading record
				$reading->report = $reportPath;
				$reading->status = $status;
				$reading->score = $score;
				$reading->completeness = $completeness;
				$reading->pronunciation_score = $pronunciationScore;
				$reading->intonation_score = $intonationScore;
				$reading->stress_score = $stressScore;
				$reading->fluency_score = $fluencyScore;
				$reading->speed_score = $speedScore;
				$reading->speed_situation = $speedSituation;
				$reading->speed = $speed;
				$reading->asr_duration = $asr_time;
				$reading->audio_length = $duration;
				$reading->save();

				echo date('Y-m-d H:i:s') . '; read_id = ' . $report['readId'] . ', status = ' . $report['status'] . PHP_EOL;
			} else {
				sleep(1);
			}
		}
	}

	/**
	 * 计算朗读的得分
	 * @author Hanxiang<hanxiang.qiu@enstar.com>
	 * @param $path
	 * @param $lesson_id
	 * @return mixed
	 */
	private function calculateScore($path, $lesson_id = 0)
	{
		$file = public_path() . $path;
		try {
			$fh = fopen($file, 'r+');
		} catch(\Exception $e) {
			echo date('Y-m-d H:i:s') . '; file open error: ' . $file . PHP_EOL;
			return 0;
		}

		$json = file_get_contents($file);
		$report = json_decode($json, true);

		// weight
		$w_pronunciation = Config::get('evaluate.w.pronunciation');
		$w_intonation = Config::get('evaluate.w.intonation');
		$w_stress = Config::get('evaluate.w.stress');
		$w_fluency = Config::get('evaluate.w.fluency');
		$w_speed = Config::get('evaluate.w.speed');

//        $completeness = $report['userTotalSentences'] / $report['totalSentences'];

		if (!$report['totalSentences']) {
			$completeness = 0;
		} else {
			$completeness = $report['userTotalSentences'] / $report['totalSentences'];
			// $completeness = round($completeness * 100, 2);
		}

		// add
		$reportFinalScore = $report['finalScore'];
		$s =  $reportFinalScore['pronunciationScore'] * $w_pronunciation;
		$s += $reportFinalScore['intonationScore']    * $w_intonation;
		$s += $reportFinalScore['stressScore']        * $w_stress;
		$s += $reportFinalScore['fluencyScore']       * $w_fluency;

		// speed score
		$speedScoreArray = SpeedScore::score($report['speed'], $lesson_id);
		$s += $speedScoreArray['speedScore'] * $w_speed * $completeness;
		// $score = $s;

		$finalScore = array();
		$finalScore['overallScore'] = $s;
		$finalScore['completeness'] = round($completeness * 100, 2);
		$finalScore['pronunciationScore'] = $reportFinalScore['pronunciationScore'];
		$finalScore['intonationScore'] = $reportFinalScore['intonationScore'];
		$finalScore['stressScore'] = $reportFinalScore['stressScore'];
		$finalScore['fluencyScore'] = $reportFinalScore['fluencyScore'];
		$finalScore['speed'] = $report['speed'];
		$finalScore['speedScore'] = $speedScoreArray['speedScore'];
		$finalScore['speedSituation'] = $speedScoreArray['speedSituation'];
		$finalScore['duration'] = isset($report['duration']) ? floor($report['duration']) : 0;

		$report['finalScore'] = $finalScore;

		file_put_contents($file, '');
		$toWriteJson = json_encode($report);
		file_put_contents($file, $toWriteJson);

		fclose($fh);
		return $finalScore;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}
