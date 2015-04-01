<?php
namespace Enstar\Controller\Rest;

use Input;
use Session;
use UserWord;
use NewWord;
use Dictionary;
use Validator;

class WordController extends BaseController
{
    /**
     * 获取我的单词本
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function getMyWords()
    {
        $user_id = Session::get('uid');

        $userWords = UserWord::where('user_id', $user_id)->get();
        if (!$userWords) {
            return $this->encodeResult('21101', 'empty set', null);
        }

        $userWordsArray = array();
        $userWordsArray['count'] = count($userWords);
        $userWordsArray['words'] = null;

        if (count($userWords)) {
            $userWordsArray['words'] = array();
            foreach ($userWords as $w) {
                $item = array();
                $item['id'] = $w->id;
                if ($w->new_words_id) {
                    $newWord = NewWord::find($w->new_words_id);
                    if (!$newWord) {
                        continue;
                    }

                    $item['word'] = $newWord->word;
                    $item['en_pronunce'] = $newWord->en_pronunce;
                    $item['en_pronunce_url'] = $newWord->en_pronunce_url;
                    $item['us_pronunce'] = $newWord->us_pronunce;
                    $item['us_pronunce_url'] = $newWord->us_pronunce_url;
                    $item['translation'] = $newWord->translation;

                } elseif ($w->dictionary_id) {
                    $dictionaryWord = Dictionary::find($w->dictionary_id);
                    if (!$newWord) {
                        continue;
                    }

                    $item['word'] = $dictionaryWord->word;
                    $item['en_pronunce'] = $dictionaryWord->en_pronunce;
                    $item['en_pronunce_url'] = $dictionaryWord->en_pronunce_url;
                    $item['us_pronunce'] = $dictionaryWord->us_pronunce;
                    $item['us_pronunce_url'] = $dictionaryWord->us_pronunce_url;
                    $item['translation'] = $dictionaryWord->translation;

                }

                $item['status'] = UserWord::$status[$w->status];
                array_push($userWordsArray['words'], $item);
            }
        }

        return $this->encodeResult('11100', 'success', $userWordsArray);
    }

    /**
     * 添加一个单词到我的单词本
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function postMyWords()
    {
        $input = Input::all();
        if (isset($input['new_words_id'])) {

            $newWord = NewWord::find($input['new_words_id']);
            if (!$newWord) {
                return $this->encodeResult('21202', 'word invalid', null);
            }

            $existUserWord = UserWord::where('user_id', Session::get('uid'))
                ->where('new_words_id', $input['new_words_id'])->count();
            if ($existUserWord) {
                return $this->encodeResult('21203', 'word has been added', null);
            }

            $userWord = new UserWord();
            $userWord->user_id = Session::get('uid');
            $userWord->new_words_id = $input['new_words_id'];
            $userWord->status = 1;
            $userWord->save();

        } elseif (isset($input['dictionary_id'])) {

            $dictWord = Dictionary::find($input['dictionary_id']);
            if (!$dictWord) {
                return $this->encodeResult('21202', 'word invalid', null);
            }

            $existUserWord = UserWord::where('user_id', Session::get('uid'))
                ->where('dictionary_id', $input['dictionary_id'])->count();
            if ($existUserWord) {
                return $this->encodeResult('21203', 'word has been added', null);
            }

            $userWord = new UserWord();
            $userWord->user_id = Session::get('uid');
            $userWord->dictionary_id = $input['dictionary_id'];
            $userWord->status = 1;
            $userWord->save();

        } else {
            return $this->encodeResult('21201', 'please set a PK', null);
        }

        return $this->encodeResult('11200', 'success', null);
    }

    /**
     * 删除我的单词本中的一个单词 TODO:批量操作
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function deleteMyWords()
    {
        $input = Input::all();
        $validator = Validator::make($input, array(
            'id' => 'required'
        ));
        if($validator->fails()){
            return $this->encodeResult('21401', $validator->messages()->first(), null);
        }

        $userWord = UserWord::find($input['id']);
        if (!$userWord) {
            return $this->encodeResult('21402', 'word invalid', null);
        }

        $userWord->status = 0;
        $userWord->save();
        $userWord->delete();
        return $this->encodeResult('11400', 'success', null);
    }

    /**
     * 已掌握一个单词 TODO:批量操作
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function completeMyWords()
    {
        $input = Input::all();
        $validator = Validator::make($input, array(
            'id' => 'required'
        ));
        if($validator->fails()){
            return $this->encodeResult('21301', $validator->messages()->first(), null);
        }

        $userWord = UserWord::find($input['id']);
        if (!$userWord) {
            return $this->encodeResult('21302', 'word invalid', null);
        }

        $userWord->status = 2;
        $userWord->save();
        return $this->encodeResult('11300', 'success', null);
    }

    /**
     * TODO 查询一个单词
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function queryWord()
    {
        $input = Input::get();
        $validator = Validator::make($input, array(
            'word' => 'required'
        ));
        if($validator->fails()){
            return $this->encodeResult('21501', $validator->messages()->first(), null);
        }

        $word = trim($input['word']);
        $dict = Dictionary::where('word', $word)->first();
        if (!$dict) {
            return $this->encodeResult('21502', 'no word', null);
        }

        $dict = $dict->toArray();
        return $this->encodeResult('11500', 'success', $dict);
    }
}
