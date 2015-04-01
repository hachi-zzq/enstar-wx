<?php namespace Enstar\Library;

use Analysis;
use Config;

/**
 * 语速speed得分
 * @author Hanxiang<hanxiang.qiu@enstar.com>
 */
class SpeedScore
{
    /**
     * 评测报告计算语速speed得分
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     * @param float $wordsPerMin 每分钟单词数
     * @param $lesson_id
     * @return array
     * @TODO
     */
    public static function score($wordsPerMin, $lesson_id = 0)
    {
        $analysis = Analysis::where('lesson_id', $lesson_id)->first();
        if (!$analysis) {
            $min = Config::get('evaluate.speed.low');
        }

        try {
            $analyisJson = file_get_contents(public_path() . $analysis->path);
            $analysisArray = json_decode($analyisJson, true);
            $min = round($analysisArray['speed']); // 语速（每分钟单词数）达到100分的下限
        } catch(\Exception $e) {
            $min = Config::get('evaluate.speed.low');
        }

        $max = Config::get('evaluate.speed.high'); // 语速（每分钟单词数）达到100分的上限
        $level = Config::get('evaluate.speed.level');

        $speedScore = 100;
        $situation = 'NORMAL';
        $wpm = round($wordsPerMin);
        if ($wpm < $min) {
            $speedScore = 100 - round(($min - $wpm) / $level);
            $situation = 'SLOW';
        } elseif ($wpm > $max) {
            $speedScore = 100 - round(($wpm - $max) / $level);
            $situation = 'FAST';
        }
        return array('speedScore' => $speedScore, 'speedSituation' => $situation);
    }
}
