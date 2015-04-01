<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DialogueDebug extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'enstar:lesson-dialogue-debug';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'book1 lesson dialogue debug';

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
	 * @第一册对话debug
	 *@paran null
	 * @return mixed
     * @author zhengqian.zhu@enstar.com
	 */
	public function fire()
	{
		$sentences = Sentence::where('type','dialogue')->get();
        foreach($sentences as $s){
            $obj = Sentence::find($s->id);
            $speaker = $obj->prefix;
            $obj->raw_sentence = preg_replace("/^$speaker\s?/",'',$obj->raw_sentence);
            $obj->save();
            echo sprintf("hander sentence ID:%d\n",$obj->id);
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
