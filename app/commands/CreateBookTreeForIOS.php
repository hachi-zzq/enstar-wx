<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateBookTreeForIOS extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nce-rocket:create-book-tree';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'create book tree for ios';

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
	 * @生成课文的目录树for ios
	 * @param null
	 * @return mixed
     * @author zhengqian.zhu@enstar.com
	 */
	public function fire()
	{
		//
        $books = DB::table('books')
            ->whereRaw("book_key != '' and deleted_at is null")
            ->groupBy('book_key')
            ->orderBy('sort')
            ->get();
        $arr = array();
        foreach($books as $b){
            array_push($arr,array(
                'book_key'=>$b->book_key,
                'name'=>$b->name,
                'title'=>$b->title,
                'subtitle'=>$b->subtitle,
                'description'=>$b->description,
                'cover'=>$b->cover,
                'publisher'=>$b->publisher,
                'sort'=>$b->sort,
            ));
            file_put_contents(public_path('book.json'),json_encode($arr));
            echo sprintf("book copy success new ID:%s\n",$b->id);
        }
        unset($arr);

        $units = DB::table('units')
            ->whereRaw("unit_key != '' and deleted_at is null")
            ->groupBy('unit_key')
            ->orderBy('sort')
            ->get();
        $arr = array();
        foreach($units as $u){
            array_push($arr,array(
                'unit_key'=>$u->unit_key,
                'name'=>$u->name,
                'sort'=>$u->sort,
            ));
            file_put_contents(public_path('unit.json'),json_encode($arr));
            echo sprintf("Unit copy success new ID:%s\n",$u->id);
        }
        unset($arr);
        $lessons = DB::table('lessons')
            ->whereRaw("lesson_key != '' and deleted_at is null")
            ->groupBy('lesson_key')
            ->orderBy('lesson_key')
            ->get();
        $arr = array();
        foreach($lessons as $l){
            array_push($arr,array(
                'lesson_key'=>$l->lesson_key,
                'title'=>preg_replace('/^Lesson\s[\d]+\s+/','',$l->title),
                'translation'=>preg_replace("/\r\n\s*/","\n",$l->translation),
//                'audio'=>$l->audio,
                'sort'=>$l->sort,
            ));
            file_put_contents(public_path('lesson.json'),json_encode($arr));
            echo sprintf("Unit copy success new ID:%s\n",$l->id);
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
