@extends('layouts.page_layout')

@section('title') 最近评分 @stop

@section('content')
<div data-wxsignature data-appid="{{$jsapiConfig->appid}}" data-timestamp="{{$jsapiConfig->timestamp}}" data-noncestr="{{$jsapiConfig->noncestr}}" data-signature="{{$jsapiConfig->signature}}" class="xiaoying sans fixHeader">
    <div class="header">
        <h1 class="pageHeading">最近评分</h1>
    </div>
    <div class="trunk">
        @if(count($recent_grade)==0)
            <div class="noData">暂无记录</div>
        @else
        <ol class="lessenList recentList">
            @foreach($recent_grade as $g)
            <li class="lessonItem"><a href="{{route('readingDetail', $g->uuid)}}" class="lessonBlock"><span class="lessonLabel">{{$g->lesson->book->title}} - Lesson {{$g->lesson->sort}}</span><span class="lessonName">{{{str_replace("Lesson ".$g->lesson->sort,"",$g->lesson->title)}}}</span><span class="lessonTime">{{(new ESDate($g->created_at))->getTime()}}</span><span class="lessonInfo"><span class="lessonStatistics">完成度<span class="statisticsFigure">{{round($g->completeness,0)}}%</span></span><span class="lessonScore">综合得分<span class="scoreFigure {{$g->score<60?'fail':''}}">{{round($g->score,0)}}</span></span></span><span class="icon iconDirect"></span></a></li>
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