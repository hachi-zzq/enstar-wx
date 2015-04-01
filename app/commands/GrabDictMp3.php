<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GrabDictMp3 extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nce-rocket:grab-dict-mp3';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'grab mp3 to local';

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
        $step = 100;
        while(true){
            $start = isset($start) ? $start : 0;
            $word = Dictionary::where('id','>',$start)->take($step)->get();
            if( ! $word->count()){
                break;
            }
            foreach($word as $w){
                if($w->en_pronunce_url == ''){
                    continue;
                }
                $res = $this->getCurl($w->en_pronunce_url);
                if($res['httpCode'] == 200){
                    if(file_exists(sprintf('C:\Users\zhu\Desktop\dict_mp3_v2\%s_en.mp3',$w->word))){
                        echo sprintf("%s.mp3 is exist\n",$w->word);
                        continue;
                    }
                    file_put_contents(sprintf("%s%s_en.mp3",'C:\Users\zhu\Desktop\dict_mp3_v2\\',$w->word),$res['data']);
//                    $w->en_pronunce_url = sprintf("/data/dict/audio/%s_en.mp3",$w->word);
                }else{
                    Log::info(sprintf("%s EN httpCode is not 200 is %d!",$w->word,$res['httpCode']));
//                    $w->en_pronunce_url = '404 NOT FOUND';
                }
//                $w->save();
                unset($res);

                if($w->us_pronunce_url == ''){
                    continue;
                }
                $res = $this->getCurl($w->us_pronunce_url);
                if($res['httpCode'] == 200){
                    if(file_exists(sprintf('C:\Users\zhu\Desktop\dict_mp3_v2\%s_us.mp3',$w->word))){
                        echo sprintf("%s.mp3 is exist\n",$w->word);
                        continue;
                    }
                    file_put_contents(sprintf("%s%s_us.mp3",'C:\Users\zhu\Desktop\dict_mp3_v2\\',$w->word),$res['data']);
//                    $w->us_pronunce_url = sprintf("/data/dict/audio/%s_us.mp3",$w->word);
                }else{
                    Log::info(sprintf("%s US httpCode is not 200 is %d!",$w->word,$res['httpCode']));
//                    $w->us_pronunce_url = '404 NOT FOUND';
                }
//                $w->save();
                echo sprintf("success write file %s.mp3\n",$w->word);
            }
            $start = $w->id;
        }

	}


    function getCurl($url,$timeOut=10){
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_TIMEOUT,$timeOut);
        $res = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return array(
            'data'=>$res,
            'httpCode'=>$status
        );
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
