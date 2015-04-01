<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CheckLessonSort extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nce-rocket:check-lesson-sort';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'check the lesson NO and lesson sort';

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
        $units = Unit::all();
        foreach($units as $u){
            if( ! preg_match('/^Unit\s[\d]+/',$u->name)){
                Log::info(sprintf("unit ID: %s",$u->id));
            }
            echo sprintf("handler unit %s\n",$u->id);
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
