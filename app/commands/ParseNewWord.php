<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ParseNewWord extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nce-rocket:parse-lesson-new-word';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'parse new word json file into DB';

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

		$arrFilePath = array(
            'C:\Users\zhu\Desktop\new_word_tmp\meta1\\',
            'C:\Users\zhu\Desktop\new_word_tmp\meta2\\',
            'C:\Users\zhu\Desktop\new_word_tmp\meta3\\',
            'C:\Users\zhu\Desktop\new_word_tmp\meta4\\'
        );
        foreach($arrFilePath as $dir){
            $dh = opendir($dir);
            while(($file=readdir($dh)) !== false){
                if($file == '.' or $file == '..'){
                    continue;
                }
                $fullPath = $dir.$file;
                $obj = json_decode(file_get_contents($fullPath));
                $obj->title = trim($obj->title,'.?!');
                $lessons = Lesson::where('title','like',"%$obj->title%")->whereRaw("deleted_at is null and lesson_key != ''");
                $lesson = $lessons->first();
                if( ! $lesson){
                    Log::info(sprintf("%s not found in DB",$fullPath));
                    continue;
                }
                if( ! $obj->words){
                    Log::info(sprintf("%s word not exist",$fullPath));
                    continue;
                }
//
//                if($obj->id != $lesson->sort){
//                    Log::info(sprintf("%s Lesson number is error",$fullPath));
//                }

//                $lessons->update(array(
//                    'translation'=>preg_replace("/\r\n\s*/","\n",$obj->translation)
//                ));


                $words = $obj->words;


                foreach($words as $k=>$w){

                    if(Dictionary::where('word', $w->word)->count() == 0){
                        Log::info(sprintf("word %s not found in Dict DB", $w->word));
                    }

                    if(NewWord::where('word', $w->word)->count()){
                        Log::info(sprintf("word %s already in  DB", $w->word));
                        continue;
                    }

                    $word = new NewWord();
                    $word->lesson_key = $lesson->lesson_key;
                    $word->word = $w->word;
                    $word->sort = $k*4;
                    $word->translation = $w->explanation;
                    $word->property = isset($w->partOfSpeech) ? $w->partOfSpeech : '';

                    //关联dict表，查找发音
                    $dict = Dictionary::where('word',$w->word)->first();
                    if($dict){
                        $word->en_pronunce = $dict->en_pronunce;
                        $word->en_pronunce_url = $dict->en_pronunce_url;
                        $word->us_pronunce = $dict->us_pronunce;
                        $word->us_pronunce_url = $dict->us_pronunce_url;
                    }else{
                        $word->en_pronunce = '';
                        $word->en_pronunce_url = '';
                        $word->us_pronunce = '';
                        $word->us_pronunce_url = '';
                    }

                    $word->save();
                }

                echo sprintf("success handler file %s \n",$fullPath);


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
