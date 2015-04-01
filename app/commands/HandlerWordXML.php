<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class HandlerWordXML extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nce-rocket:handler-word-xml';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'parse xml and write into DB';

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
	 * @将抓取到的xml处理入库
	 * @return mixed
     * @author zhengqian.zhu
	 */
	public function fire()
	{
		$dir = 'C:\Users\zhu\Desktop\word_xml\\';
        $dh = opendir($dir);
        while(($file=readdir($dh)) !== false){
            if($file == '.' or $file == '..'){
                continue;
            }
            $fullFile = $dir.$file;
            $fullFile = str_replace("\\",'/',$fullFile);
            $xml = simplexml_load_file($fullFile);
            $json = json_decode(json_encode($xml));
            if( ! isset($json->key)){
                Log::info(sprintf("%s the key is not exist",$file));
                continue;
            }
            if(Dictionary::where('word',$json->key)->count()){
                Log::info(sprintf("word %s already in DB",$json->key));
                continue;
            }
            echo sprintf("word : %s \n",$json->key);
            $dict = new Dictionary();
            $dict->word = $json->key;
            //发音与英标
            if(isset($json->ps) and isset($json->pron)){
                if(count($json->ps) != count($json->pron)){
                    Log::info(sprintf("%s ps and pron line is not eq",$json->key));
//                    continue;
                }
                if( !is_array($json->ps) and ! is_array($json->pron)){
                    Log::info(sprintf("%s ps and pron is only one !!!",$json->key));
//                    continue;
                }
                $dict->en_pronunce = isset($json->ps) ? (is_array($json->ps) ? $json->ps[0] : $json->ps) : '';
                $dict->en_pronunce_url = isset($json->pron) ? (is_array($json->pron) ? $json->pron[0] : $json->pron) : '';
                $dict->us_pronunce = isset($json->ps) ? (is_array($json->ps) ? $json->ps[1] : '') : '';
                $dict->us_pronunce_url = isset($json->pron) ? (is_array($json->pron) ? $json->pron[1] : '') : '';
            }

            //词义和词性
            if(isset($json->pos) and isset($json->acceptation)){
                if(count($json->pos) != count($json->acceptation)){
                    Log::info(sprintf("%s pos and acceptation line is not eq",$json->key));
                    continue;
                }
                if( ! is_array($json->pos) and ! is_array($json->acceptation)){
                    $arr = array();
                    if( is_object($json->pos)){
                        $json->pos = '';
                    }
                    $arr[0] = array(
                        'pos'=>str_replace("\n","",$json->pos),
                        'acceptation'=>str_replace("\n","",$json->acceptation)
                    );
                    $dict->property = json_encode($arr);
                }else{
                    $arr = array();
                    foreach($json->pos as $k=>$v){
                        if(is_object($v)){
                            $v = '';
                        }
                        array_push($arr,array(
                            'pos'=>str_replace("\n",'',$v),
                            'acceptation'=>str_replace("\n",'',$json->acceptation[$k])
                        ));
                    }
                    $dict->property = json_encode($arr);
                }
                unset($arr);
            }

            //例句
            if(isset($json->sent)){
                if( ! is_array($json->sent)){
                    Log::info(sprintf("%s sent is not Array",$json->key));
                    $json->sent->orig = str_replace("\n",'',$json->sent->orig);
                    $json->sent->trans = str_replace("\n",'',$json->sent->trans);
                }else{
                    foreach($json->sent as &$s){
                        $s->orig = str_replace("\n",'',$s->orig);
                        $s->trans = str_replace("\n",'',$s->trans);
                    }
                }
                $dict->example_sentences = json_encode($json->sent);
            }
            $dict->save();
            echo sprintf("success handler word %s\n",$json->key);

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
