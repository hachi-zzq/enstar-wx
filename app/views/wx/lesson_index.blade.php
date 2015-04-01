@extends('layouts.page_layout')

@section('title') 课文列表 @stop

@section('content')
<div data-wxsignature data-appid="{{$jsapiConfig->appid}}" data-timestamp="{{$jsapiConfig->timestamp}}" data-noncestr="{{$jsapiConfig->noncestr}}" data-signature="{{$jsapiConfig->signature}}"  class="xiaoying sans fixHeader">
    <h1 class="header"><span class="cover coverNCE{{{substr($book->book_key,1)}}}"></span><span class="bookName">{{$book->title}}</span><span class="bookSlogen">{{$book->subtitle}}</span></h1>
    <div class="trunk">
        <ol class="lessenList">
            @foreach($lessons as $l)
            <li class="lessonItem"><a href="{{route('lessonDetail',$l->guid)}}" class="lessonBlock"><span class="lessonLabel">Lesson {{$l->sort}}</span><span class="lessonName">{{{str_replace("Lesson ".$l->sort,"",$l->title)}}}</span><span class="icon iconDirect"></span></a></li>
            @endforeach
        </ol>
    </div>
</div>
@stop


@section('js')
<script>
    !function(e,m){"use strict";e.weChatConfig&&(e.weChatConfig({jsApiList:["hideMenuItems"]}),m.ready(function(){m.hideMenuItems({menuList:["menuItem:share:appMessage","menuItem:share:timeline","menuItem:share:qq","menuItem:share:weiboApp","menuItem:favorite","menuItem:share:facebook","menuItem:share:QZone","menuItem:copyUrl","menuItem:originPage","menuItem:readMode","menuItem:openWithQQBrowser","menuItem:openWithSafari","menuItem:share:email","menuItem:share:brand"]})}))}(window,wx);
    //# sourceMappingURL=/assets/scripts/disablemenuitems.js.map
</script>
@stop