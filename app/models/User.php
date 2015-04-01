<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class User extends Eloquent
{

    use SoftDeletingTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    protected $dates = ['deleted_at'];

    public function getIpLookUp()
    {
        $api = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=%s";

        return file_get_contents(sprintf($api, $this->ip));

    }

    /**
     * @param $openid
     * @return mixed
     */
    public static function getByOpenId($openid)
    {
        return self::where('openid',$openid)->first();
    }


    /**
     * @保存用户信息
     * @param，openid返回的用户json
     * @author zhengqian,zhu
     */
    public function saveWxUser($userJson)
    {
        if( ! $userJson){
            echo "param is required";
            exit;
        }
        $obj = json_decode($userJson);
        $user = User::where("openid",$obj->openid)->first();
        if( ! $user){
            $user = new User();
        }
        $user->nickname = $obj->nickname;
        $user->openid = $obj->openid;
        $user->sex = $obj->sex;
        $user->province = $obj->province;
        $user->city = $obj->city;
        $user->country = $obj->country;
        $user->headimgurl = $obj->headimgurl;
        $user->headimgurl = $obj->headimgurl;
        $user->subscribe = $obj->subscribe;
        $user->unionid = isset($obj->unionid) ? $obj->unionid : null;
        $user->save();

        return true;

    }

}
