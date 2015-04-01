<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class NewWordNotInDict extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nce-rocket:grab-newword-not-in-dict';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'grab the new word not in dict';

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
        $url = "http://dict-co.iciba.com/api/dictionary.php?w=go&key=B7A08D04943A86BDCE19E2E27DEDDDEB";
        $dicts = DB::table('dictionary')->select('word')->get();
        $arr = array();
        foreach($dicts as $d){
            array_push($arr,$d->word);
        }
        $res = DB::table('new_words')
            ->whereNotIn('word',$arr)->get();

        foreach($res as $r){
            file_put_contents(sprintf("%s%s.xml","C:/Users/zhu/Desktop/word_xml_v2/",$r->word),file_get_contents(sprintf("http://dict-co.iciba.com/api/dictionary.php?w=%s&key=B7A08D04943A86BDCE19E2E27DEDDDEB",$r->word)));
            echo sprintf("success grab xml %s.xml\n",$r->word);
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
