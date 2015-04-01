<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateLessonKey extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nce-rocket:create-lesson-key';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'create lesson key,such as B1L1';

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
		//$books = Book::whereRaw("book_key != '' and deleted_at is null")->get();
        $units = Unit::all();
        foreach($units as $u){
            $objBook = Book::find($u->book_id);
            if($objBook->book_key == ''){
                continue;
            }
            $obj = Unit::find($u->id);
            preg_match('/^Unit\s([\d]+)/',$obj->name,$match);
            $num = $match[1];
            $obj->unit_key = sprintf("%sU%s",$objBook->book_key,$num);
            $obj->save();
            echo sprintf("Handler Unit ID %s \n",$u->id);
        }


        $lessons = Lesson::all();
        foreach($lessons as $l){
            $objBook = Book::find($l->book_id);
            if($objBook->book_key == ''){
                continue;
            }
            $obj = Lesson::find($l->id);
            $title = $obj->title;
            preg_match('/^Lesson\s([\d]+)/',$title,$match);
            $num = $match[1];
            if($l->unit_id == ''){
                $unit_key = "U0";
                $obj->lesson_key = sprintf("%s%sL%s",$objBook->book_key,$unit_key,$num);
            }else{
                $unit_key = Unit::find($l->unit_id)->unit_key;
                $obj->lesson_key = sprintf("%sL%s",$unit_key,$num);

            }

            $obj->save();
            echo sprintf("Handler lesson ID %s Title %s\n",$l->id,$title);
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
