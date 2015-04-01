<?php

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use J20\Uuid\Uuid;
use Enstar\Library\LeanCloudService\Push;
//use RedisLog;
// use Reading;

class NRQueueListen extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nce-rocket:queue-listen';

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
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$key = Config::get('app.rocket_read_out_key');
		$redis = Redis::connection();

		if (!$redis) {
			return false;
		}

		while (true) {
			try {
				$reportStr = $redis->rpop($key);
			}
			catch(\Exception $e) {
                echo date('Y-m-d H:i:s') . '; redis error.' . PHP_EOL;
				continue;
			}

			if (!empty($reportStr)) {
				// save redis log
				$redisLog = new RedisLog();
				$redisLog->key = $key;
				$redisLog->content = $reportStr;
				$redisLog->save();

				$report = json_decode($reportStr, true);

				if (!$report ||
					!isset($report['readId']) ||
					!isset($report['status']) ||
					!isset($report['reportPath'])) {
					echo date('Y-m-d H:i:s') . '; get a invalid msg: ' . $reportStr . PHP_EOL;
					continue;
				}

				try {
                    $reading = Reading::find($report['readId']);
                } catch (\Exception $e) {
                    DB::reconnect('mysql');
                    echo date('Y-m-d H:i:s') . '; DB reconnect.' . PHP_EOL;
                    $reading = Reading::find($report['readId']);
                }

				if (!$reading) { continue; }

				// handle report
                $reportPath = '';
                $score = 0;

				if ($report['status'] == 'SUCCESS') {
					$reportPath = $report['reportPath'];
					$status = 100;
                    $score = $this->calculateScore(); // TODO

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
					Push::send($reading->user_id, Lesson::getLessonTitle($reading->lesson_id) . ' 评分已完成，快来看看吧。');

				} elseif ($report['status'] == 'PROCESSING') {
					$status = 10;
				} else { // FAIL
					$status = -1;
				}

				// update reading record
                $reading->report = $reportPath;
                $reading->status = $status;
                $reading->grade = $score;
                $reading->save();

				echo date('Y-m-d H:i:s') . '; get a msg: read_id = ' . $report['readId'] . ', status = ' . $report['status'] . PHP_EOL;
			} else {
				echo date('Y-m-d H:i:s') . '; no report.' . PHP_EOL;
				sleep(1);
			}
		}
	}

	/**
	 * TODO 计算朗读的得分
     * @author Hanxiang<hanxiang.qiu@enstar.com>
	 */
	private function calculateScore()
	{
        return 80;
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
