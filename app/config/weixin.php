<?php
$domainName = Config::get('app.url');
return array(
    'id' => 'gh_31efb2f535cc',//'gh_e510d3577a76',//微信服务号ID
    'appID' => 'wxbc879a670cb32971',//'wx22be62c5077abcfe',
    'appsecret' =>'20b517a9882cecc620e20175f8943ef0',// 'f364efc269da0f33e7f376e0ca4c9cc0',

    'api' => array(
        'token' => "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential",
        'create_menu' => 'https://api.weixin.qq.com/cgi-bin/menu/create',
        'set_industry' => 'https://api.weixin.qq.com/cgi-bin/template/api_set_industry',
        'web_auth'=>'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code',
        'get_user_info'=>'https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=%s',
        'snsapi_base'=>'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base&state=%s#wechat_redirect',
        'get_jsapi_ticket'=>'https://api.weixin.qq.com/cgi-bin/ticket/getticket'
    ),

    'template_id'=>'JkYsPHGDgKdsKbH4S6M455IxKF7IFo6qOyKU5q-IiTA',
    'greetings'=>'欢迎关注EnStar小英，在这里您可以听、读课文，我们将会为您打分。请点击下面菜单进行功能选择。',
    'about'=>'EnStar小英是EnStar英语在线评分微信服务号，涵盖新概念1至4册所有课文，随时随地想学就学！朗读课文获得评分，边学边读提高口语！',
    'industry' => '{"industry_id1":"1","industry_id2":"16"}',//行业
    //微信菜单
    'menu' =><<<MENU
{
                "button": [
                  {
                    "name": "教材",
                    "sub_button": [
                      {
                        "type": "view",
                        "name": "新概念1册",
                        "url": "$domainName/lesson/5/index"
                      },
                      {
                        "type": "view",
                        "name": "新概念2册",
                        "url": "$domainName/lesson/6/index"
                      },
                      {
                        "type": "view",
                        "name": "新概念3册",
                        "url": "$domainName/lesson/9/index"
                      },
                      {
                        "type": "view",
                        "name": "新概念4册",
                        "url": "$domainName/lesson/11/index"
                      },
                      {
                        "type": "click",
                        "name": "搜索",
                        "key": "SEARCH_LESSON"
                      }
                    ]
                  },
                  {
                    "name": "小英评分",
                    "sub_button": [
                      {
                        "type": "click",
                        "name": "关于小英",
                        "key": "ABOUT_XY"
                      },
                      {
                        "type": "view",
                        "name": "下载APP",
                        "url": "https://itunes.apple.com/app/id957599977"
                      }
                    ]
                  },
                  {
                    "name": "我的",
                    "sub_button": [
                      {
                        "type": "view",
                        "name": "我收藏的",
                        "url": "$domainName/favorite/index"
                      },
                      {
                        "type": "view",
                        "name": "我读过的",
                        "url": "$domainName/reading/index"
                      }
                      ,
                      {
                        "type": "view",
                        "name": "最近评分",
                        "url": "$domainName/grade/recent"
                      }
                    ]
                  }
                ]
              }
MENU
,

);


