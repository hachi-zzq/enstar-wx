@extends('layouts.admin_layout')

@section('title') 阅读记录 @stop

@section('content')
<div id="content-wrapper" xmlns="http://www.w3.org/1999/html">
<div class="page-header">
    <h1><span class="text-light-gray">用户内容 / </span>阅读记录</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">

        <div class="panel">

            <div class="panel-heading">
                <span class="panel-title">阅读记录</span>
            </div>

            @if (Session::get('tips'))
            <div class="alert alert-page alert-dark">
                <button type="button" class="close" data-dismiss="alert">×</button>
                {{ Session::get('tips') }}
            </div>
            @endif
            @if (Session::get('error_tips'))
            <div class="alert alert-page alert-danger alert-dark">
                <button type="button" class="close" data-dismiss="alert">×</button>
                {{ Session::get('error_tips') }}
            </div>
            @endif
            @if (Session::get('success_tips'))
            <div class="alert alert-page alert-success alert-dark">
                <button type="button" class="close" data-dismiss="alert">×</button>
                {{ Session::get('success_tips') }}
            </div>
            @endif

            <script>
                init.push(function () {
                    var options = {
                        todayBtn: "linked",
                        orientation: $('body').hasClass('right-to-left') ? "auto right" : 'auto auto',
                        format: 'yyyy-mm-dd'
                    }
                    $('#start_time').datepicker(options);
                    $('#end_time').datepicker(options);
                })
            </script>



            <div class="panel">
                <form method="get" action="{{{route('adminReadingList')}}}">
                <div class="panel-body">
                    <div class="col-xs-2">
                        <input type="text" class="form-control" id="start_time" name="start_time" value="{{{$input['start_time'] or ''}}}" placeholder="Start date">
                    </div>

                    <div class="col-xs-2">
                        <input type="text" class="form-control" id="end_time" name="end_time" value="{{{$input['end_time'] or ''}}}" placeholder="End date">
                    </div>
                    @foreach(Input::except('start_time','end_time') as $k=>$i)
                    <input type="hidden" name="{{$k}}" value="{{$i}}">
                    @endforeach
                    <button type="submit" class="btn btn-success" >筛选</button>
                    <a href="{{{route('adminReadingList')}}}"><button type="button" class="btn btn-success" >清空</button></a>

                    <a href="/admin/reading/index?start_time={{$arrTime['timeTody']}}&end_time={{$arrTime['timeNow']}}" style="margin-left: 20px"><button type="button" class="btn btn-success" >今天</button></a>
                    <a href="/admin/reading/index?start_time={{$arrTime['timeYes']}}&end_time={{$arrTime['timeTody']}}"><button type="button" class="btn btn-success" >昨天</button></a>
                    <a href="/admin/reading/index?start_time={{$arrTime['timeNowWeek']}}&end_time={{$arrTime['timeNow']}}"><button type="button" class="btn btn-success" >本周</button></a>
                </div>
                </form>

            </div>
<!---->
<!--            <div class="input-daterange input-group" id="bs-datepicker-range">-->
<!--                <input type="text" class="input-sm form-control" name="start" placeholder="Start date">-->
<!--                <span class="input-group-addon">to</span>-->
<!--                <input type="text" class="input-sm form-control" name="end" placeholder="End date">-->
<!--            </div>-->
            <div class="panel-body">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>阅读人（ 手机号 / Token ）</th>
                        <th>课文（ 语言 ）</th>
                        <th>录音地址 ( 试听 )</th>
                        <th>录音时长 （秒）</th>
                        <th>评分状态</th>
                        <th>评分用时 （秒）</th>
                        <th>得分</th>
                        <th>评级（0 - 10）</th>
                        <th>完成度（ % ）</th>
                        <th>语速（词 / 分）</th>
                        <th>维度 （发音/语调/重音/流畅/语速）</th>
                        <th>阅读时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($readings as $r)
                    <tr>
                        <td>{{{$r->id}}}</td>
                        <td>{{{$r->user}}}</td>
                        <td>{{{$r->lesson}}} ( {{{$r->lang}}} )</td>
                        <td title="试听">
                            <a href="{{{$r->audio_url}}}" target="_blank" ><i class="menu-icon fa fa-volume-up"></i> 试听</a>
                        </td>
                        <td>{{{$r->duration or 0}}}</td>
                        <td><a href="#" class="label {{{$r->status['statusClass']}}}">{{{$r->status['status']}}}</a></td>
                        <td>{{{$r->asr_duration or 0}}}</td>
                        <td>{{{$r->score}}}</td>
                        <td>{{{$r->grade}}}</td>
                        <td>{{{$r->completeness}}}</td>
                        <td>{{{$r->speed}}}</td>
                        <td>{{{$r->pronunciation_score or '无'}}},{{{$r->intonation_score or '无'}}},{{{$r->stress_score or '无'}}},{{{$r->fluency_score or '无'}}},{{{$r->speed_score or '无'}}}</td>
                        <td>{{{$r->created_at}}}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /5. $DEFAULT_TABLES -->
        <div class="row pagination-demo">
            <div class="col-sm-12">
                <ul class="pagination">
                    {{$readings->appends(Input::except('page'))->links()}}
                </ul>
            </div>
        </div>
    </div>
</div>



</div>
@stop

@section('js')
<script>
    function createUnit(){
        $.ajax({
            url:'/admin/unit/createUnit',
            type:'POST',
            data:$('#unit_create').serializeArray(),
            success:function(ret){
                alert(ret.message);
                if(ret.msg_code == 0){
                    window.location.reload();
                }
            },
            error:function(){
                alert('ajax error');
            }
        })

    }

    function appendValues(book_name,unit_name,unit_id){
        $('#book_name').attr('value',book_name);
        $('#unit_name').attr('value',unit_name);
        $('#unit_id').attr('value',unit_id);
    }

    function modifyUnit(){
        $.ajax({
            url:'/admin/unit/modify',
            type:'POST',
            data:$('#unit_modify').serializeArray(),
            success:function(ret){
                alert(ret.message);
                if(ret.msg_code == 0){
                    window.location.reload();
                }
            },
            error:function(){
                alert('ajax error');
            }
        })

    }
</script>
@stop