@extends('layouts.page_layout')

@section('title') 评分报告 @stop

@section('content')
<div data-wxsignature data-appid="{{$jsapiConfig->appid}}" data-timestamp="{{$jsapiConfig->timestamp}}" data-noncestr="{{$jsapiConfig->noncestr}}" data-signature="{{$jsapiConfig->signature}}" class="xiaoying sans fixHeader">
    <div class="header">
        <h1 class="pageHeading">评分报告 - 朗读{{{date('YmdHis',strtotime($reading->created_at))}}}</h1>
    </div>
    <div data-wxshare data-title="{{$reading->lesson->book->title}}第{{substr($reading->lesson->lesson_key,-1)}}课得分{{round($reading->score,0)}}分 - EnStar小英" data-image="{{asset('/static/img/nce_square_' . substr($reading->lesson->book->book_key, 1) . '.png')}}" data-description="发音：{{round($reading->pronunciation_score,0)}}，语调：{{round($reading->intonation_score,0)}}，重音：{{round($reading->stress_score,0)}}，流畅：{{round($reading->fluency_score,0)}}，语速：{{round($reading->speed_score,0)}}" class="trunk">
        <div class="resultSummary">
            <h2 class="resultHeading"><span class="resultLabel">{{{$reading->lesson->book->title}}}</span><span class="resultName">{{{$reading->lesson->title}}}</span></h2>
            <p class="resultTime">{{$reading->created_at}}</p>
            <div class="progress">
                <div class="progressBar"></div>
            </div>
            <p class="progressLabel">完成度<span class="progressFigure">{{$reading->completeness}}%</span></p>
            <p class="resultScore"><span class="resultScoreFigure">{{round($reading->score,0)}}</span>综合得分</p>
        </div>
        <div class="resultDetail">
            <h3 class="hidden">各维度评分</h3>
            <ul class="dimensionList clearfix">
                <li class="dimensionItem">发音<span class="dimensionScore @if($reading->pronunciation_score == 100) {{'pass'}} @elseif($reading->pronunciation_score<60) {{'fail'}} @endif">{{round($reading->pronunciation_score,0)}}</span><span class="icon iconDirect"></span></li>
                <li class="dimensionItem">语调<span class="dimensionScore @if($reading->intonation_score == 100) {{'pass'}} @elseif($reading->intonation_score<60) {{'fail'}} @endif">{{round($reading->intonation_score,0)}}</span><span class="icon iconDirect"></span></li>
                <li class="dimensionItem">重音<span class="dimensionScore @if($reading->stress_score == 100) {{'pass'}} @elseif($reading->stress_score<60) {{'fail'}} @endif">{{round($reading->stress_score,0)}}</span><span class="icon iconDirect"></span></li>
                <li class="dimensionItem">流畅<span class="dimensionScore @if($reading->fluency_score == 100) {{'pass'}} @elseif($reading->fluency_score<60) {{'fail'}} @endif">{{round($reading->fluency_score,0)}}</span><span class="icon iconDirect"></span></li>
                <li class="dimensionItem">语速<span class="dimensionScore @if($reading->speed_score == 100) {{'pass'}} @elseif($reading->speed_score<60) {{'fail'}} @endif">{{round($reading->speed_score,0)}}</span><span class="icon iconDirect"></span></li>
            </ul>
            <div class="dimensionClose"><span class="icon iconClose"></span></div>
            <div class="dimensionDetails">
                <!--发音-->
                <div class="dimensionDetail">
                    @if($reading->pronunciation_score==100)
                    <h3 class="dimensionConclusion dimensionConclusionPerfect"><span class="dimensionResult">发音得分<span class="dimensionResultFigure">100</span></span><span class="dimensionCongrats">太棒了，发音完全正确哦！</span></h3>
                    @else
                    <h3 class="dimensionConclusion "><span class="icon iconWarning"></span>以下单词<span class="fail">发音</span>不正确，需要注意<span class="dimensionResult">发音得分<span class="dimensionResultFigure">{{round($reading->pronunciation_score,0)}}</span></span></h3>
                    <div class="text">
                        <p>
                            @foreach($error_detail as $s)
                            @foreach($s['word'] as $w)
                            {{str_replace('</p><p>','',$w['text_pronunciation'])}}
                            @endforeach
                            @endforeach
                        </p>
                    </div>
                    @endif
                </div>
                <!--语调-->
                <div class="dimensionDetail">
                    @if($reading->intonation_score==100)
                    <h3 class="dimensionConclusion dimensionConclusionPerfect"><span class="dimensionResult">语调得分<span class="dimensionResultFigure">100</span></span><span class="dimensionCongrats">太棒了，语调完全正确哦！</span></h3>
                    @else
                    <h3 class="dimensionConclusion"><span class="icon iconWarning"></span>以下单词<span class="fail">语调</span>不正确，需要注意<span class="dimensionResult">语调得分<span class="dimensionResultFigure">{{round($reading->intonation_score,0)}}</span></span></h3>
                    <div class="text">
                        <p>
                            @foreach($error_detail as $s)
                                @foreach($s['word'] as $w)
                                {{str_replace('</p><p>','',$w['text_intonation'])}}
                                @endforeach
                            @endforeach
                        </p>
                    </div>
                    @endif

                </div>

                <!--重音-->
                <div class="dimensionDetail">
                    @if($reading->stress_score==100)
                    <h3 class="dimensionConclusion dimensionConclusionPerfect"><span class="dimensionResult">重音得分<span class="dimensionResultFigure">100</span></span><span class="dimensionCongrats">太棒了，重音完全正确哦！</span></h3>
                    @else
                    <h3 class="dimensionConclusion"><span class="icon iconWarning"></span>以下单词<span class="fail">重音</span>不正确，需要注意<span class="dimensionResult">重音得分<span class="dimensionResultFigure">{{round($reading->stress_score,0)}}</span></span></h3>
                    <div class="text">
                        <p>
                            @foreach($error_detail as $s)
                            @foreach($s['word'] as $w)
                            {{str_replace('</p><p>','',$w['text_stress'])}}
                            @endforeach
                            @endforeach
                        </p>
                    </div>
                    @endif
                </div>

                <!--流畅-->
                <div class="dimensionDetail">
                    @if($reading->fluency_score == 100)
                    <h3 class="dimensionConclusion dimensionConclusionPerfect"><span class="dimensionResult">流畅得分<span class="dimensionResultFigure">100</span></span><span class="dimensionCongrats">太棒了，流畅完全正确哦！</span></h3>
                    @else
                    <h3 class="dimensionConclusion"><span class="icon iconWarning"></span>以下单词<span class="fail">流畅</span>不正确，需要注意<span class="dimensionResult">流畅得分<span class="dimensionResultFigure">{{round($reading->fluency_score,0)}}</span></span></h3>
                    <div class="text">
                        <p>
                            @foreach($error_detail as $s)
                            @foreach($s['word'] as $w)
                            {{str_replace('</p><p>','',$w['text_fluency'])}}
                            @endforeach
                            @endforeach
                        </p>
                    </div>
                    @endif
                </div>

                <!--语速-->
                <div class="dimensionDetail">
                    <h3 class="dimensionConclusion">阅读语速<span class="icon iconWarning"></span><span class="fail">{{$reading->speed>110 ? '快' :($reading->speed<60 ? '慢' : '适中')}}</span><span class="dimensionResult">语速得分<span class="dimensionResultFigure">{{round($reading->speed_score,0)}}</span></span></h3>
                    <div class="text">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="follow">
        <ul class="followList">
            <li class="followItem"> <span class="followGuide">点击右上角图标&rsaquo;查看公众号&rsaquo;关注我们！</span></li>
            <li class="followItem"><span class="followGuide">或搜索微信公众号<span class="hint">EnStar小英</span>,关注我们！</span></li>
        </ul><img src="/assets/images/qr@2x.png" class="qr"/>
        <div class="qrHint">长按二维码，关注我们</div>
    </div>
</div>
@stop

@section('js')

<script type="text/javascript">
<?php require_once public_path('assets/scripts/result.js')?>
</script>
@stop