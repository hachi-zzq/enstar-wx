<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateNewWordJson extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nce-rocket:create-new-word-json';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'create new word json for IOS';

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
		//
        $step = 100;
        $arr = array();
        while(true){
            $start = isset($start) ? $start : 0;
            $word = NewWord::where('id','>',$start)->take($step)->get();
            if( ! $word->count()){
                file_put_contents(public_path('new_words.json'),json_encode($arr));
                break;
            }

            foreach($word as $w){
                $arrPro[] = array(
                    'property'=>$w->property,
                    'translation'=>$w->translation
                );

                $en_pronunce_exist = $w->en_pronunce_url ? true : false;
                $us_pronunce_exist = $w->us_pronunce_url ? true : false;
                array_push($arr,array(
                    'lesson_key'=>$w->lesson_key,
                    'word'=>$w->word,
                    'sort'=>$w->sort,
                    'en_pronunce_exist'=>$en_pronunce_exist,
                    'us_pronunce_exist'=>$us_pronunce_exist,
//                    'en_pronunce'=>$w->en_pronunce,
//                    'en_pronunce_url'=>$w->en_pronunce_url != '' ? substr($w->en_pronunce_url,17) : '',
//                    'us_pronunce'=>$w->us_pronunce,
//                    'us_pronunce_url'=>$w->us_pronunce_url != '' ? substr($w->us_pronunce_url,17) : '',
                    'property'=>json_encode($arrPro)
                ));
                unset($arrPro);
                echo sprintf("handler dict ID: %s\n",$w->word);
            }
            $start = $w->id;
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
