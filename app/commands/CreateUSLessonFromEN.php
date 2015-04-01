<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateUSLessonFromEN extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'enstar:create-US-lesson-from-EN';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'create US lesson from EN lesson';

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
		//处理第一册
//        return $this->firstBook();

        //第二册
//        $this->secondBook();

        //第三册
//        $this->thirdBook();
        //第四册
        $this->FourthBook();
	}


    /**
     * @第一册处理
     * @从第一册英音--->美音
     * @author zhengqian.zhu@enstar.com
     */
    public function firstBook(){
        $firstENBookId = 5;
        $firstUSBookId = 7;
        $objBook = Book::find($firstENBookId);
        $objUSBook = Book::find($firstUSBookId);
        if( ! $objBook || !$objUSBook){
            exit("the book not found");
        }

        $lessons = Lesson::where('book_id',$firstENBookId)->get();
        //拷贝lesson
        foreach($lessons as $l){
            $objNewLesson = new Lesson();
            $uuid = \J20\Uuid\Uuid::v4(false);
            $objNewLesson->title = $l->title;
            $objNewLesson->guid = $uuid;
            $objNewLesson->raw_content = $l->raw_content;
            $objNewLesson->asr_content = $l->asr_content;
            $objNewLesson->audio = sprintf("/data/audio/%s/%s.m4a",date("Ymd"),$uuid);
            $objNewLesson->book_id = $firstUSBookId;
            $objNewLesson->unit_id = null;
            $objNewLesson->sort = $l->sort;
            $objNewLesson->tag = $l->tag;
            $objNewLesson->version = $l->version;
            $objNewLesson->lesson_unique = $uuid;
            $objNewLesson->status = $l->status;
            $objNewLesson->save();
            //拷贝sentence
            $lessonId = $objNewLesson->id;
            $sentence = Sentence::where('lesson_id',$l->id)->get();
            foreach($sentence as $s){
                $sent = new Sentence();
                $sent->lesson_id = $lessonId;
                $sent->raw_sentence = $s->raw_sentence;
                $sent->asr_sentence = $s->asr_sentence;
                $sent->sort = $s->sort;
                $sent->prefix = $s->prefix;
                $sent->format = $s->format;
                $sent->type = $s->type;
                $sent->save();
            }
            echo sprintf("Lesson ID: %s copy complete!\n",$l->id);
        }
        return 0;

    }

    /**
     * @第二册处理
     * @从第二册英音--->美音
     * @author zhengqian.zhu@enstar.com
     */
    public function secondBook()
    {
        $ENBookId = 6;
        $USBookId = 8;
        $objBook = Book::find($ENBookId);
        $objUSBook = Book::find($USBookId);
        if( ! $objBook || !$objUSBook){
            exit("the book not found");
        }

        $units = Unit::where('book_id',$ENBookId)->get();
        foreach($units as $u){
            //遍历单元
            $USUnit = new Unit();
            $USUnit->name = $u->name;
            $USUnit->book_id = $USBookId;
            $USUnit->sort = $u->sort;
            $USUnit->unit_unique = \J20\Uuid\Uuid::v4(false);
            $USUnit->status = $u->status;
            $USUnit->save();
            $USUnitId = $USUnit->id;

            //遍历课文
            $lessons = Lesson::where('unit_id',$u->id)->where('book_id',$ENBookId)->get();
            foreach($lessons as $l){
                $objNewLesson = new Lesson();
                $uuid = \J20\Uuid\Uuid::v4(false);
                $objNewLesson->title = $l->title;
                $objNewLesson->guid = $uuid;
                $objNewLesson->raw_content = $l->raw_content;
                $objNewLesson->asr_content = $l->asr_content;
                $objNewLesson->audio = sprintf("/data/audio/%s/%s.m4a",'20141228',$uuid);
                $objNewLesson->book_id = $USBookId;
                $objNewLesson->unit_id = $USUnitId;
                $objNewLesson->sort = $l->sort;
                $objNewLesson->tag = $l->tag;
                $objNewLesson->version = $l->version;
                $objNewLesson->lesson_unique = $uuid;
                $objNewLesson->status = $l->status;
                $objNewLesson->save();
                $USLessonId = $objNewLesson->id;

                //遍历句子
                $sentence = Sentence::where('lesson_id',$l->id)->get();
                foreach($sentence as $s){
                    $sent = new Sentence();
                    $sent->lesson_id = $USLessonId;
                    $sent->raw_sentence = $s->raw_sentence;
                    $sent->asr_sentence = $s->asr_sentence;
                    $sent->sort = $s->sort;
                    $sent->prefix = $s->prefix;
                    $sent->format = $s->format;
                    $sent->type = $s->type;
                    $sent->save();
                }
                echo sprintf("Lesson ID: %s copy complete!\n",$l->id);
            }

            echo sprintf("Unit ID: %s copy complete!\n",$u->id);

        }

    }

    /**
     * @第三册美音
     * @author zhengqian.zhu@enstar.com
     */
    public function thirdBook(){
        $ENBookId = 9;
        $USBookId = 10;
        $objBook = Book::find($ENBookId);
        $objUSBook = Book::find($USBookId);
        if( ! $objBook || !$objUSBook){
            exit("the book not found");
        }

        $units = Unit::where('book_id',$ENBookId)->get();
        foreach($units as $u){
            //遍历单元
            $USUnit = new Unit();
            $USUnit->name = $u->name;
            $USUnit->book_id = $USBookId;
            $USUnit->sort = $u->sort;
            $USUnit->unit_unique = \J20\Uuid\Uuid::v4(false);
            $USUnit->status = $u->status;
            $USUnit->save();
            $USUnitId = $USUnit->id;

            //遍历课文
            $lessons = Lesson::where('unit_id',$u->id)->where('book_id',$ENBookId)->get();
            foreach($lessons as $l){
                $objNewLesson = new Lesson();
                $uuid = \J20\Uuid\Uuid::v4(false);
                $objNewLesson->title = $l->title;
                $objNewLesson->guid = $uuid;
                $objNewLesson->raw_content = $l->raw_content;
                $objNewLesson->asr_content = $l->asr_content;
                $objNewLesson->audio = sprintf("/data/audio/%s/%s.m4a",'20150101',$uuid);
                $objNewLesson->book_id = $USBookId;
                $objNewLesson->unit_id = $USUnitId;
                $objNewLesson->sort = $l->sort;
                $objNewLesson->tag = $l->tag;
                $objNewLesson->version = $l->version;
                $objNewLesson->lesson_unique = $uuid;
                $objNewLesson->status = $l->status;
                $objNewLesson->save();
                $USLessonId = $objNewLesson->id;

                //遍历句子
                $sentence = Sentence::where('lesson_id',$l->id)->get();
                foreach($sentence as $s){
                    $sent = new Sentence();
                    $sent->lesson_id = $USLessonId;
                    $sent->raw_sentence = $s->raw_sentence;
                    $sent->asr_sentence = $s->asr_sentence;
                    $sent->sort = $s->sort;
                    $sent->prefix = $s->prefix;
                    $sent->format = $s->format;
                    $sent->type = $s->type;
                    $sent->save();
                }
                echo sprintf("Lesson ID: %s copy complete!\n",$l->id);
            }

            echo sprintf("Unit ID: %s copy complete!\n",$u->id);

        }
    }

    /**
     * @第四册美音处理脚本
     * @author zhengqian.zhu@enstar.com
     *
     */
    public function FourthBook()
    {
        $ENBookId = 11;
        $USBookId = 12;
        $objBook = Book::find($ENBookId);
        $objUSBook = Book::find($USBookId);
        if( ! $objBook || !$objUSBook){
            exit("the book not found");
        }

        $units = Unit::where('book_id',$ENBookId)->get();
        foreach($units as $u){
            //遍历单元
            $USUnit = new Unit();
            $USUnit->name = $u->name;
            $USUnit->book_id = $USBookId;
            $USUnit->sort = $u->sort;
            $USUnit->unit_unique = \J20\Uuid\Uuid::v4(false);
            $USUnit->status = $u->status;
            $USUnit->save();
            $USUnitId = $USUnit->id;

            //遍历课文
            $lessons = Lesson::where('unit_id',$u->id)->where('book_id',$ENBookId)->get();
            foreach($lessons as $l){
                $objNewLesson = new Lesson();
                $uuid = \J20\Uuid\Uuid::v4(false);
                $objNewLesson->title = $l->title;
                $objNewLesson->guid = $uuid;
                $objNewLesson->raw_content = $l->raw_content;
                $objNewLesson->asr_content = $l->asr_content;
                $objNewLesson->audio = sprintf("/data/audio/%s/%s.m4a",'20150102',$uuid);
                $objNewLesson->book_id = $USBookId;
                $objNewLesson->unit_id = $USUnitId;
                $objNewLesson->sort = $l->sort;
                $objNewLesson->tag = $l->tag;
                $objNewLesson->version = $l->version;
                $objNewLesson->lesson_unique = $uuid;
                $objNewLesson->status = $l->status;
                $objNewLesson->save();
                $USLessonId = $objNewLesson->id;

                //遍历句子
                $sentence = Sentence::where('lesson_id',$l->id)->get();
                foreach($sentence as $s){
                    $sent = new Sentence();
                    $sent->lesson_id = $USLessonId;
                    $sent->raw_sentence = $s->raw_sentence;
                    $sent->asr_sentence = $s->asr_sentence;
                    $sent->sort = $s->sort;
                    $sent->prefix = $s->prefix;
                    $sent->format = $s->format;
                    $sent->type = $s->type;
                    $sent->save();
                }
                echo sprintf("Lesson ID: %s copy complete!\n",$l->id);
            }

            echo sprintf("Unit ID: %s copy complete!\n",$u->id);

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
