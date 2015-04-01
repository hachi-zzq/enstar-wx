<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateDictJson extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nce-rocket:create-dict-json';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'create-dict-json';

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
            $word = Dictionary::where('id','>',$start)->take($step)->get();
            if( ! $word->count()){
                file_put_contents(public_path('dict.json'),json_encode($arr));
                break;
            }

            foreach($word as $w){
                if(empty($w->en_pronunce) and empty($w->en_pronunce_url) and empty($w->us_pronunce) and empty($w->us_pronunce_url) and empty($w->property)){
                    continue;
                }

                array_push($arr,array(
                   'word'=>$w->word,
                   'en_pronunce'=>$w->en_pronunce,
//                   'en_pronunce_url'=>$w->en_pronunce_url != '' ? substr($w->en_pronunce_url,17) : '',
                   'us_pronunce'=>$w->us_pronunce,
//                   'us_pronunce_url'=>$w->us_pronunce_url != '' ? substr($w->us_pronunce_url,17) : '',
                   'property'=>$w->property
//                   'example_sentences'=>$w->example_sentences,
                ));

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
