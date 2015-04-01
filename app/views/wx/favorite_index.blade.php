@extends('layouts.page_layout')

@section('title') 我收藏的 @stop

@section('content')
<div data-wxsignature data-appid="{{$jsapiConfig->appid}}" data-timestamp="{{$jsapiConfig->timestamp}}" data-noncestr="{{$jsapiConfig->noncestr}}" data-signature="{{$jsapiConfig->signature}}" class="xiaoying sans fixHeader">
    <div class="header">
        <h1 class="pageHeading">我收藏的</h1>
        <p class="favouriteCount"><span class="favouriteCountFigure">{{count($favorites)}}</span>条</p>
    </div>
    <div class="trunk">
        @if(count($favorites)==0)
        <div class="noData">暂无记录</div>
        @else
        <ol class="lessenList favouriteList">
            @foreach($favorites as $f)
            <li class="lessonItem"><a href="{{route('lessonDetail',$f->lesson->guid)}}" class="lessonBlock"><span class="lessonLabel">{{$f->lesson->book->title}} - Lesson {{$f->lesson->sort}}</span><span class="lessonName">{{{str_replace("Lesson ".$f->lesson->sort,"",$f->lesson->title)}}}</span><span class="lessonTime">{{(new ESDate($f->created_at))->getTime()}}</span><span class="icon iconDirect"></span></a></li>
            @endforeach
        </ol>
        @endif
    </div>
</div>
@stop

@section('js')
<script>
    !function(e,m){"use strict";e.weChatConfig&&(e.weChatConfig({jsApiList:["hideMenuItems"]}),m.ready(function(){m.hideMenuItems({menuList:["menuItem:share:appMessage","menuItem:share:timeline","menuItem:share:qq","menuItem:share:weiboApp","menuItem:favorite","menuItem:share:facebook","menuItem:share:QZone","menuItem:copyUrl","menuItem:originPage","menuItem:readMode","menuItem:openWithQQBrowser","menuItem:openWithSafari","menuItem:share:email","menuItem:share:brand"]})}))}(window,wx);
    //# sourceMappingURL=/assets/scripts/disablemenuitems.js.map
</script>
@stop
