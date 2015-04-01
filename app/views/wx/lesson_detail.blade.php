@extends('layouts.page_layout')

@section('title')阅读课文@stop

@section('css')
<link rel="stylesheet" href="/assets/stylesheets/article.css"/>
@stop

@section('content')
<div data-wxsignature data-appid="{{$jsapiConfig->appid}}" data-timestamp="{{$jsapiConfig->timestamp}}" data-noncestr="{{$jsapiConfig->noncestr}}" data-signature="{{$jsapiConfig->signature}}" class="xiaoying sans">
    <h1 class="articleHeader"><span class="lessonLabel">{{{$lesson->book->title}}}</span><span class="articleName">{{{$lesson->title}}}</span><span class="icon iconStar @if(UserFavorite::isExist($lesson->id,$userId)) {{'iconStarred'}} @endif" id="favorite"></span></h1>
    <div data-wxshare data-title="我学习了{{$lesson->book->title}}第{{substr($lesson->lesson_key,-1)}}课 - EnStar小英" data-image="{{asset('/static/img/nce_square_' . substr($lesson->book->book_key, 1) . '.png')}}" data-description="{{$lesson->title}}" class="text">
        <p>@foreach($lesson->sentences as $s){{str_replace('</p><p>','',$s->raw_sentence)}} @endforeach </p>
    </div>
    <div class="followRule"></div>
    <div class="follow playerSpacer">
        <ul class="followList">
            <li class="followItem"> <span class="followGuide">点击右上角图标&rsaquo;查看公众号&rsaquo;关注我们！</span></li>
            <li class="followItem"><span class="followGuide">或搜索微信公众号<span class="hint">EnStar小英</span>,关注我们！</span></li>
        </ul><img src="/assets/images/qr@2x.png" class="qr"/>
        <div class="qrHint">长按二维码，关注我们</div>
    </div>
    <div id="player" v-attr="class: 'player ' + status + (repeating ? ' repeating' : '') + (trackPlaying ? ' trackPlaying' : '')" data-lessonid="{{$lesson->guid}}" class="player"><span class="spliter"></span><span v-on="click: playClick" data-play data-src="{{Config::get('app.audio_domain')}}{{$lesson->audio}}" data-duration="{{$lesson->duration}}" class="button buttonPlay"><span class="ctrl ctrlPlay"></span><span class="ctrl ctrlPause"></span><span class="ctrl ctrlContinue"></span></span><span v-on="click: recordClick" class="button buttonRecord"><span class="ctrl ctrlRecord"></span><span class="ctrl ctrlCut"></span><span class="ctrl ctrlConfirm"></span></span><span v-on="click: stopClick" class="button buttonSlave buttonStop"><span class="ctrl ctrlStop"></span></span><span v-on="click: repeatClick" class="button buttonSlave buttonRepeat"><span class="ctrl ctrlRepeat"></span><span class="ctrl ctrlRepeatOff"></span></span><span v-on="click: cancelClick" class="button buttonSlave buttonCancel"><span class="ctrl ctrlCancel"></span></span><span v-on="click: redoClick" class="button buttonSlave buttonRedo"><span class="ctrl ctrlRedo"></span></span><span v-on="click: trackPlayClick" class="button buttonSlave buttonPlayTrack"><span class="ctrl ctrlPlayTrack"></span><span class="ctrl ctrlPauseTrack"></span></span><span class="recordHint"><span class="ctrl ctrlRecording"></span><span class="ctrl ctrlRecordingOff"></span><span v-text="status === 'recording' ? recordTime : '试听或提交'" class="recordInfo"></span></span></div>
</div>
@stop

@section('js')
<!--<script src="/assets/scripts/vue.js"></script>-->

<script type="text/javascript">

<?php require_once public_path('assets/scripts/vue.js')?>

</script>

<!--<script src="/assets/scripts/article.js"></script>-->

<script type="text/javascript">
<?php require_once public_path('assets/scripts/article.js')?>
</script>

@stop