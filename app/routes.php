<?php

Route::get('/', function () {
    return "Hello EnStar-XiaoYing";
});

Route::get('test','HomeController@test');

/**
 * 提交反馈
 */
Route::post('feedback', array('as' => 'feedback', 'uses' => 'HomeController@postFeedback'));

/**
 * 通行证
 */
Route::group(array('prefix' => 'passport'), function () {
    Route::get('signin', array('as' => 'signin', 'uses' => 'PassportController@signin')); //登陆
    Route::get('logout', array('as' => 'logout', 'uses' => 'PassportController@logout')); //退出
    Route::post('signin', array('as' => 'signin_post', 'uses' => 'PassportController@postSignIn')); //登陆处理

});

/**
 * Auth
 * @author zhengqian,zhu
 */
Route::group(array('prefix' => 'auth'), function () {
    Route::get('checkToken', array('as' => 'checkToken', 'uses' => 'AuthController@checkToken')); //checkToken
});

/**
 * WeiXin Route
 * @author zhengqian.zhu
 */
Route::group(array('prefix'=>'','before'=>'wx-auth-base'),function(){
    Route::group(array('prefix'=>'lesson'),function(){
        Route::get('{book_id}/index',array('as'=>'lessonIndex','uses'=>'Enstar\Controller\Weixin\LessonController@index'));
        Route::get('{lesson_guid}/detail',array('as'=>'lessonDetail','uses'=>'Enstar\Controller\Weixin\LessonController@lessonDetail'));
    });

    Route::group(array('prefix'=>'reading'),function(){
        Route::get('index/{user_id?}',array('as'=>'readingIndex','uses'=>'Enstar\Controller\Weixin\ReadingController@index'));
        Route::get('{reading_uuid}/detail',array('as'=>'readingDetail','uses'=>'Enstar\Controller\Weixin\ReadingController@detail'));
        Route::get('{reading_uuid}/errorDetail',array('as'=>'readingErrorDetail','uses'=>'Enstar\Controller\Weixin\ReadingController@errorDetail'));
        Route::any('save',array('as'=>'saveMedia','uses'=>'Enstar\Controller\Weixin\ReadingController@saveMedia'));
    });

    Route::group(array('prefix'=>'grade'),function(){
        Route::get('recent/{user_id?}',array('as'=>'rencentGrade','uses'=>'Enstar\Controller\Weixin\ReadingController@recentGrade'));
        Route::get('{reading_id}/detail',array('as'=>'gradeDetail','uses'=>'Enstar\Controller\Weixin\ReadingController@detail'));
    });

    Route::group(array('prefix'=>'favorite'),function(){
        Route::get('index/{user_id?}',array('as'=>'FavoriteIndex','uses'=>'Enstar\Controller\Weixin\FavoriteController@index'));
    });

});
//微信授权
Route::get('/weixin/auth-base',array('as'=>'userAuth','uses'=>'Enstar\Controller\Weixin\AuthController@authBase'));

/**
 * admin route
 * @author zhengqian.zhu@enstar.com
 * @return null
 */

Route::group(array('before' => 'admin-auth','prefix'=>'admin'), function()
{

    /** index */
    Route::get('/', array('as' => 'adminHome', 'uses' => 'Enstar\Controller\Admin\HomeController@index'));

    /** book */
    Route::group(array('prefix' => 'book'), function () {
        Route::get('index', array('as' => 'adminBookIndex', 'uses' => 'Enstar\Controller\Admin\BookController@index'));
        Route::get('create', array('as' => 'adminBookCreate', 'uses' => 'Enstar\Controller\Admin\BookController@create'));
        Route::post('create', array('as' => 'adminBookPostCreate', 'uses' => 'Enstar\Controller\Admin\BookController@postCreate'));
        Route::post('sort', array('as' => 'adminBookSort', 'uses' => 'Enstar\Controller\Admin\BookController@sort'));
        Route::get('{book_id}/createNewVersion', array('as' => 'adminBookNewVersion', 'uses' => 'Enstar\Controller\Admin\BookController@createNewVersion'));
        Route::get('{book_id}/destroy', array('as' => 'adminBookDestroy', 'uses' => 'Enstar\Controller\Admin\BookController@destroy'));
        Route::get('{book_id}/publish', array('as' => 'adminBookPublish', 'uses' => 'Enstar\Controller\Admin\BookController@publish'));
        Route::get('{book_id}/history', array('as' => 'adminBookHistory', 'uses' => 'Enstar\Controller\Admin\BookController@history'));//TODO
    });

    /** lesson */
    Route::group(array('prefix' => 'lesson'), function () {
        Route::get('{book_id}/index', array('as' => 'adminLessonIndex', 'uses' => 'Enstar\Controller\Admin\LessonController@index'));
        Route::get('{book_id}/createLesson', array('as' => 'adminLessonCreate', 'uses' => 'Enstar\Controller\Admin\LessonController@create'));
        Route::post('create', array('as' => 'adminLessonPostCreate', 'uses' => 'Enstar\Controller\Admin\LessonController@postCreate'));
        Route::get('{lesson_id}/detail', array('as' => 'adminLessonDetail', 'uses' => 'Enstar\Controller\Admin\LessonController@detail'));
        Route::get('{lesson_id}/destroy', array('as' => 'adminLessonDestroy', 'uses' => 'Enstar\Controller\Admin\LessonController@destroy'));
        Route::get('{lesson_id}/modify', array('as' => 'adminLessonModify', 'uses' => 'Enstar\Controller\Admin\LessonController@modify'));
        Route::post('modify', array('as' => 'adminLessonPostModify', 'uses' => 'Enstar\Controller\Admin\LessonController@postModify'));
        Route::get('{lesson_id}/correct', array('as' => 'adminLessonCorrect', 'uses' => 'Enstar\Controller\Admin\LessonController@correct'));
        Route::post('correct', array('as' => 'adminLessonPostCorrect', 'uses' => 'Enstar\Controller\Admin\LessonController@postCorrect'));
        Route::get('{lesson_id}/rehash/page/{page_num?}', array('as' => 'adminLessonRehash', 'uses' => 'Enstar\Controller\Admin\LessonController@rehash'));
        Route::get('{lesson_id}/history', array('as' => 'adminLessonHistory', 'uses' => 'Enstar\Controller\Admin\LessonController@history')); //TODO
        Route::post('multiSort', array('as' => 'adminLessonMutiSort', 'uses' => 'Enstar\Controller\Admin\LessonController@multiSort'));

    });

    /** unit */
    Route::group(array('prefix' => 'unit'), function () {
        Route::get('index', array('as' => 'adminUnitIndex', 'uses' => 'Enstar\Controller\Admin\UnitController@index'));
        Route::post('modify', array('as' => 'adminUnitModify', 'uses' => 'Enstar\Controller\Admin\UnitController@modify'));
        Route::post('createUnit', array('as' => 'adminUnitCreate', 'uses' => 'Enstar\Controller\Admin\UnitController@postCreate'));
        Route::get('{unit_id}/destroy', array('as' => 'adminUnitDestroy', 'uses' => 'Enstar\Controller\Admin\UnitController@destroy'));
        Route::post('multiSort', array('as' => 'adminUnitMutiSort', 'uses' => 'Enstar\Controller\Admin\UnitController@multiSort'));
    });

    /** user */
    Route::group(array('prefix' => 'user'), function () {
        Route::get('index/{type?}', array('as' => 'adminUserIndex', 'uses' => 'Enstar\Controller\Admin\UserController@index'));
        Route::get('create', array('as' => 'adminUserCreate', 'uses' => 'Enstar\Controller\Admin\UserController@create'));
        Route::post('create', array('as' => 'adminUserPostCreate', 'uses' => 'Enstar\Controller\Admin\UserController@postCreate'));
        Route::get('{user_id}/modify', array('as' => 'adminUserModify', 'uses' => 'Enstar\Controller\Admin\UserController@modify')); //TODO
        Route::post('modify', array('as' => 'adminUserPostModify', 'uses' => 'Enstar\Controller\Admin\UserController@postModify')); //TODO
        Route::get('{user_id}/destroy', array('as' => 'adminUserDestroy', 'uses' => 'Enstar\Controller\Admin\UserController@destroy')); //TODO
    });


    /** reading */
    Route::group(array('prefix' => 'reading'), function () {
        Route::get('index', array('as' => 'adminReadingList', 'uses' => 'Enstar\Controller\Admin\ReadingController@index'));
    });

    /** report */
    Route::group(array('prefix' => 'report'), function () {
        Route::get('index', array('as' => 'adminReportIndex', 'uses' => 'Enstar\Controller\Admin\ReportController@index'));
    });

    /** notice */
    Route::group(array('prefix' => 'notice'), function () {
        Route::get('index', array('as' => 'adminNoticeIndex', 'uses' => 'Enstar\Controller\Admin\NoticeController@notice'));
        Route::get('{notice_id}/modify', array('as' => 'adminNoticeModify', 'uses' => 'Enstar\Controller\Admin\NoticeController@modify'));
        Route::post('postModify', array('as' => 'adminNoticePostModify', 'uses' => 'Enstar\Controller\Admin\NoticeController@postModify'));
        Route::get('{notice_id}/destroy', array('as' => 'adminNoticeDestroy', 'uses' => 'Enstar\Controller\Admin\NoticeController@destroy'));
    });

    /** advisory */
    Route::group(array('prefix' => 'advisory'), function () {
        Route::get('index', array('as' => 'adminAdvisoryIndex', 'uses' => 'Enstar\Controller\Admin\AdvisoryController@index'));
    });
    /** passport */
    Route::group(array('prefix' => 'system'), function () {

    });
    /**queue */
    Route::group(array('prefix' => 'queue'), function () {
        Route::group(array('prefix' => 'reading'), function () {
            Route::get('index', array('as' => 'readingQueue', 'uses' => 'Enstar\Controller\Admin\QueueController@readingIndex'));
        });
        Route::group(array('prefix' => 'lesson'), function () {
            Route::get('index', array('as' => 'lessonQueue', 'uses' => 'Enstar\Controller\Admin\QueueController@lessonIndex'));
        });
        Route::group(array('prefix' => 'retry'), function () {
            Route::get('index', array('as' => 'retryQueue', 'uses' => 'Enstar\Controller\Admin\QueueController@retryIndex'));
        });
        Route::get('flushKey/{type}', array('as' => 'flushKey', 'uses' => 'Enstar\Controller\Admin\QueueController@flushKey'));
    });

    /** test */
    Route::any('test', 'Enstar\Controller\Admin\HomeController@test');


});


/**
 * 微信接口
 */
Route::group(array('prefix' => '/rest'), function () {
    Route::group(array('prefix' => 'v1'), function () {
        Route::get('/', array('as' => 'checkSignature', 'uses' => 'Enstar\Controller\Rest\WeixinController@checkSignature'));
        Route::post('/', array('as' => 'index', 'uses' => 'Enstar\Controller\Rest\WeixinController@index'));
        Route::any('/lesson/{lesson_guid}/like', array('as' => 'addFavoriteLesson', 'uses' => 'Enstar\Controller\Rest\WeixinController@addFavoriteLesson'));
        Route::any('/lesson/{lesson_guid}/unLike', array('as' => 'delFavoriteLesson', 'uses' => 'Enstar\Controller\Rest\WeixinController@unFavoriteLesson'));
    });
});
