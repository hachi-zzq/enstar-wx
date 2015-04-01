<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Enstar\Library\MQ;

class DirAudioConvert extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'enstar:audio-dir-convert';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'audio-convert, arg:DIR';

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
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$arrDir = array(
            public_path('data/audio/20150102'),
            public_path('data/audio/20150101'),
        );
        foreach($arrDir as $dir){
            if( ! is_dir($dir)){
                echo sprintf("%s is not exist\n",$dir);
                continue;
            }
            $subDH = opendir($dir);
            while(($subFile=readdir($subDH)) !== false){
                if($subFile == '.' or $subFile == '..'){
                    continue;
                }
                $arr = explode(".",$subFile);
                if(strtolower($arr[count($arr)-1]) != 'm4a'){
                    continue;
                }
                $fileName = $dir."/".$subFile;
                $redis = MQ::getInstance();
                $redis->pushAudioConvert($fileName);
                echo sprintf("%s push into redis\n",$fileName);
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
