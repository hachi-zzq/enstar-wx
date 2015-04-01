<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/**
 * admin auth filter
 */

Route::filter('admin-auth', function()
{
    if (!Auth::check()) {
        return Redirect::route('signin');
    }

});

/**
 * @微信snsapi_base认证
 * @author zhengqian.zhu
 */
Route::filter('wx-auth-base',function()
{
//    Session::put('openid','o0SRtt6i8mI_qVYGrJmI_9SpG_TM');
    $openid = Session::get('openid');

    if( ! $openid){
        Session::put('request_url',Request::getUri());
        $authUrl = route("userAuth");
        $snsapi_base = sprintf(Config::get('weixin.api.snsapi_base'),Config::get('weixin.appID'),urlencode($authUrl),'enstar123456');
        return \Redirect::to($snsapi_base);
    }
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() !== Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

Route::filter('auth.api', function()
{
    $token = Request::input('Nce-Rocket-Application-Token');
	$token = $token ? $token : Request::header('Nce-Rocket-Application-Token');
	if (!$token) {
		return Response::json(array(
			"request_id" => 0,
			"msgcode" => "20001",
			"message" => "please set a token",
			"response" => null,
			"version" => "v0.1",
			"servertime" => time()
		));
	}

	$user = User::where('token', $token)->first();
	if (!$user) {
		return Response::json(array(
			"request_id" => 0,
			"msgcode" => "20002",
			"message" => "token invalid",
			"response" => null,
			"version" => "v0.1",
			"servertime" => time()
		));
	}

	if ($user->token_expiration < date('Y-m-d H:i:s')) {
		return Response::json(array(
			"request_id" => 0,
			"msgcode" => "20003",
			"message" => "token expired",
			"response" => null,
			"version" => "v0.1",
			"servertime" => time()
		));
	}

	Session::put('uid', $user->id);
});
