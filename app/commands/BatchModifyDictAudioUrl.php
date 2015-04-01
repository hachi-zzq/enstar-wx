<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class BatchModifyDictAudioUrl extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nce-rocket:batch-modify-audio-url';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'modify-dict-audio-url';

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
        while(true){
            $start = isset($start) ? $start : 0;
            $word = Dictionary::where('id','>',$start)->take($step)->get();
            if( ! $word->count()){
                break;
            }

            foreach($word as $w){
                if($w->en_pronunce_url == null){
                    $w->en_pronunce_url = '';
                }

                if( ! $w->en_pronunce_url == ''){
                    $w->en_pronunce_url = sprintf("/data/dict/audio/%s_en.m4a",$w->word);
                }

                if($w->us_pronunce_url == null){
                    $w->us_pronunce_url = '';
                }

                if( ! $w->us_pronunce_url == ''){
                    $w->us_pronunce_url = sprintf("/data/dict/audio/%s_us.m4a",$w->word);
                }
                $w->save();
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
