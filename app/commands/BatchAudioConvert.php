<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Enstar\Library\MQ;

class BatchAudioConvert extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'enstar:batch-lessonAudio-convert';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'lesson-audio-convert';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 *@批量完成已经添加的课文的转码
	 * @return mixed
     * @author zhengqian.zhu@enstar.com
	 */
	public function fire()
	{
		$audioPath = public_path('data/audio/');
        $audioPath = str_replace("\\",'/',$audioPath);
        $dh = opendir($audioPath);
        while(($file=readdir($dh)) !== false){
            /**
             * @去掉. ..
             */
            if($file == '.' or $file == '..'){
                continue;
            }
            $subDir = $audioPath.$file;
            if( ! is_dir($subDir)){
                continue;
            }

            $subDH = opendir($subDir);
            while(($subFile=readdir($subDH)) !== false){
                if($subFile == '.' or $subFile == '..'){
                    continue;
                }
                $arr = explode(".",$subFile);
                if(strtolower($arr[count($arr)-1]) != 'm4a'){
                    continue;
                }

                $fileName = $subDir."/".$subFile;
                $redis = MQ::getInstance();
                $redis->pushAudioConvert($fileName);

            }


        }

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(

		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(

		);
	}

}
