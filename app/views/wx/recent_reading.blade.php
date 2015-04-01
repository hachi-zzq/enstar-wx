@extends('layouts.page_layout')

@section('title') 我读过的 @stop

@section('content')
<div data-wxsignature data-appid="{{$jsapiConfig->appid}}" data-timestamp="{{$jsapiConfig->timestamp}}" data-noncestr="{{$jsapiConfig->noncestr}}" data-signature="{{$jsapiConfig->signature}}"  class="xiaoying sans fixHeader">
    <div class="header">
        <h1 class="pageHeading">我读过的</h1>
    </div>
    <div class="trunk">
        @if(count($reading)==0)
        <div class="noData">暂无记录</div>
        @else
        <ol class="lessenList historyList">
            @foreach($reading as $r)
            <li class="lessonItem">
                <h2 class="lessonBlock"><span class="lessonLabel">{{{$r['lesson']->book->title}}}</span><span class="lessonName">{{{$r['lesson']->title}}}</span><span class="lessonInfo"><span class="lessonStatistics">朗读数<span class="statisticsFigure"></span></span>
                        <span class="lessonScore">最高综合得分<span class="scoreFigure {{$r['max']<60?'fail':''}}">{{round($r['max'],0)}}</span></span></span><span class="collapseIndicator"><span class="icon iconExpand"></span><span class="icon iconCollapse"></span></span></h2>
                <ol class="recordList">
                    @foreach($r['reading'] as $read)
                    <li class="recordItem"><a href="{{{route('readingDetail',$read['uuid'])}}}" class="recordBlock"><span class="recordTime">{{$read['created_at']}}</span><span class="recordName">朗读{{{date('YmdHis',strtotime($read['created_at']))}}}</span><span class="recordInfo"><span class="recordStatistics">完成度<span class="statisticsFigure">{{$read['completeness']}}</span></span>
                                <span class="recordScore">综合得分<span class="scoreFigure {{$read['score']<60?'fail':''}}" >{{round($read['score'],0)}}</span></span></span><span class="icon iconDirect"></span></a></li>
                    @endforeach
                </ol>
            </li>
            @endforeach
        </ol>
        @endif
    </div>
</div>
@stop

@section('js')
<script>!function(e,n){"use strict";function t(e,t,r){for(var a=!0;a;){a=!1;var c=e,o=t,l=r;if(l=l||n.documentElement,s(c,o))return c;if(c===l||c.parentNode==n||!c.parentNode)return;e=c.parentNode,t=o,r=l,a=!0}}function r(e,n){s(e,n)?c(e,n):a(e,n,!0)}function a(e,n,t){(t||!s(e,n))&&(e.className=(e.className?(e.className+" ").replace(u," "):"")+n)}function c(e,n){for(var t=" "+e.className+" ";t.indexOf(" "+n+" ")>-1;)t=t.replace(" "+n+" "," ");e.className=o(t)}function s(e,n){return 1===e.nodeType&&(" "+e.className+" ").replace(u," ").indexOf(" "+n+" ")>-1}function o(e){return null==e?"":(e+"").replace(i,"")}var l=n.querySelector(".historyList");l.addEventListener("click",function(e){var n=t(e.target,"lessonBlock",l);n&&(e.preventDefault(),r(t(n,"lessonItem",l),"lessonItemExpanded"))},!1);var u=/[\t\r\n\f]/g,i=/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g}(window,document);
    //# sourceMappingURL=/assets/scripts/history.js.map
</script>

<script>
    !function(e,m){"use strict";e.weChatConfig&&(e.weChatConfig({jsApiList:["hideMenuItems"]}),m.ready(function(){m.hideMenuItems({menuList:["menuItem:share:appMessage","menuItem:share:timeline","menuItem:share:qq","menuItem:share:weiboApp","menuItem:favorite","menuItem:share:facebook","menuItem:share:QZone","menuItem:copyUrl","menuItem:originPage","menuItem:readMode","menuItem:openWithQQBrowser","menuItem:openWithSafari","menuItem:share:email","menuItem:share:brand"]})}))}(window,wx);
    //# sourceMappingURL=/assets/scripts/disablemenuitems.js.map
</script>
@stop

