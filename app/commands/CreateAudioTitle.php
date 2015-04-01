<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateAudioTitle extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nce-rocket:create-lesson_key-title';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'modify the audio lesson audio title for lesson_key';

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
        $arrLang = array(
            5=>'_en',
            6=>'_en',
            7=>'_us',
            8=>'_us',
            9=>'_en',
            10=>'_us',
            11=>'_en',
            12=>'_us',
        );

        $lesson = Lesson::whereRaw("book_id in (5,6,7,8,9,10,11,12) and deleted_at is null")->get();
        foreach($lesson as $l){
//            $pathInfo = pathinfo($l->audio);
            $audioPath = public_path(trim($l->audio,'/'));
            if(file_exists($audioPath)){
//                rename($audioPath,public_path('data/audio_nce/').$l->lesson_key.$arrLang[$l->book_id].'.m4a');
                copy($audioPath,public_path('data/audio_nce/').$l->lesson_key.$arrLang[$l->book_id].'.m4a');
                echo sprintf("success rename file %s\n",$l->id);
            }else{
                \Log::info(sprintf("Lesson %s not found\n",$l->id));
                echo sprintf("Lesson %s not found\n",$l->id);
                continue;
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
