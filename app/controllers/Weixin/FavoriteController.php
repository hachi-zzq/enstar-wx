<?php namespace Enstar\Controller\Weixin;

use \UserFavorite;
use \Input;
use \View;

class FavoriteController extends BaseController
{

    /**
     * @收藏列表
     * @param null
     * @return null
     * @author zhengqian.zhu
     */
    public function index($userId=null)
    {
        if(empty($userId)){
            $userId = $this->getUserIdFromOpenId();
        }
        $objFavorite = UserFavorite::where('user_id',$userId)->orderBy('created_at','DESC')->get();

        return View::make('wx.favorite_index')
            ->with('favorites',$objFavorite)
            ->with('jsapiConfig', $this->getJsapiConfig())
            ;

    }



}
