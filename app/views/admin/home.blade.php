@extends('layouts.admin_layout')

@section('title') 首页 @stop
@section('content')
<div id="content-wrapper">
<div class="page-header">
    <h1><span class="text-light-gray"></span>Dashboard</h1>
</div> <!-- / .page-header -->

<script>
    init.push(function () {
        $('#toggle-mme').click(function () {
            $('body').toggleClass('mme');
        });
        $('#toggle-mmc').click(function () {
            $('body').toggleClass('mmc');
        });
    });
</script>

<div class="row">
<div class="col-sm-7">


    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title">今日阅读</span>
            <div class="panel-heading-controls">
                <a href=""><a href="{{{route('adminReadingList')}}}"><span class="label label-primary">更多</span></a>
            </div>
        </div>

        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>课文（ 语言 ）</th>
                <th>试听</th>
                <th>得分</th>
                <th>ip</th>
                <th>评分状态</th>
                <th>时间</th>
            </tr>
            </thead>
            <tbody>
            @if(count($readings))
            @foreach($readings as $r)
            <tr>
                <td>{{{$r->id}}}</td>
                <td>{{{$r->lesson}}} ( {{{$r->lang}}} )</td>
                <td title="{{{$r->audio_url}}}">
                     <a href="{{{$r->audio_url}}}" target="_blank" ><i class="menu-icon fa fa-volume-up"></i>试听</a>
                </td>
                <td>{{{$r->score}}}</td>
                <td>{{{$r->ip}}}</td>
                <td><a href="#" class="label {{{$r->status['statusClass']}}}">{{{$r->status['status']}}}</a></td>
                <td>{{{$r->created_at}}}</td>
            </tr>
            @endforeach
            @endif

            </tbody>
        </table>
    </div>

    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title">昨日阅读</span>
            <div class="panel-heading-controls">
                <a href=""><a href="{{{route('adminReadingList')}}}"><span class="label label-primary">更多</span></a>
            </div>
        </div>

        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>课文（ 语言 ）</th>
                <th>录音地址</th>
                <th>得分</th>
                <th>ip</th>
                <th>评分状态</th>
                <th>时间</th>
            </tr>
            </thead>
            <tbody>
            @if(count($readingsTwo))
            @foreach($readingsTwo as $r)
            <tr>
                <td>{{{$r->id}}}</td>
                <td>{{{$r->lesson}}} ( {{{$r->lang}}} )</td>
                <td title="{{{$r->audio_url}}}">
                    <a href="{{{$r->audio_url}}}" target="_blank" ><i class="menu-icon fa fa-volume-up"></i>试听</a>
                </td>
                <td>{{{$r->score}}}</td>
                <td>{{{$r->ip}}}</td>
                <td><a href="#" class="label {{{$r->status['statusClass']}}}">{{{$r->status['status']}}}</a></td>
                <td>{{{$r->created_at}}}</td>
            </tr>
            @endforeach
            @endif

            </tbody>
        </table>
    </div>
    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title ">新用户</span>
            <div class="panel-heading-controls">
                <a href="{{{route('adminUserIndex')}}}"><span class="label label-primary">更多</span></a>
            </div>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>OpenID</th>
                <th>昵称</th>
                <th>性别</th>
                <th>地址</th>
                <th>关注时间</th>
            </tr>
            </thead>
            <tbody>
            @if(count($users))
            @foreach($users as $user)
            <tr>
                <td>{{$user->id}}</td>
                <td>{{{$user->openid}}}</td>
                <td>{{{$user->nickname}}}</td>
                <td>{{{$user->sex == 1 ? '男' : '女'}}}</td>
                <td>{{{$user->province.$user->city}}}</td>
                <td>{{{$user->created_at}}}</td>
            </tr>
            @endforeach
            @endif

            </tbody>
        </table>
    </div>


</div>
<div class="col-sm-5">


    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title">队列信息</span>
            <div class="panel-heading-controls">
            </div>
        </div>
        <table class="table">
            <tbody>
            <tr>
                <td><code>语音评测状态：</code></td>
                <td>
                    {{{$redis['redisNceStatus']}}}
                </td>
            </tr>
            <tr>
                <td><code>课文匹配队列数：</code></td>
                <td>
                    {{{$redis['redisNceAnalyzeInput']}}}
                </td>
            </tr>
            <tr>
                <td><code>阅读匹配队列：</code></td>
                <td>
                    {{{$redis['redisNceReadInput']}}}
                </td>
            </tr>
            <tr>
                <td><code>重试队列数：</code></td>
                <td>
                    {{{$redis['redisNceRetry']}}}
                </td>
            </tr>


            </tbody>
        </table>
    </div>

    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title">数据概览</span>
            <div class="panel-heading-controls">
            </div>
        </div>
        <table class="table">
            <tbody>
            <tr>
                <td><code>今日阅读数：</code></td>
                <td>
                    {{{count($readings)}}}
                </td>
            </tr>
            <tr>
                <td><code>今日注册数：</code></td>
                <td>
                    {{{count($regToday)}}}
                </td>
            </tr>


            </tbody>
        </table>
    </div>


    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title">系统信息</span>
            <div class="panel-heading-controls">
            </div>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th>系统变量</th>
                <th>变量值</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><code>服务器操作系统:</code></td>
                <td>
                    {{{PHP_OS}}}
                </td>
            </tr>
            <tr>
                <td><code>Web 服务器:</code></td>
                <td>
                    {{{$_SERVER["SERVER_SOFTWARE"]}}}
                </td>
            </tr>
            <tr>
                <td><code>PHP 版本:</code></td>
                <td>
                    {{{PHP_VERSION}}}
                </td>
            </tr>
            <tr>
                <td><code>PHP运行方式：</code></td>
                <td>
                    {{{php_sapi_name()}}}
                </td>
            </tr>

            <tr>
                <td><code>安全模式:</code></td>
                <td>
                    {{{(boolean) ini_get('safe_mode') ?  '是':'否' }}}
                </td>
            </tr>
            <tr>
                <td><code>上传附件限制：</code></td>
                <td>
                    {{{ini_get('upload_max_filesize')}}}
                </td>
            </tr>
            <tr>
                <td><code>执行时间限制：</code></td>
                <td>
                    {{{ini_get('max_execution_time').'秒'}}}
                </td>
            </tr>
            <tr>
                <td><code>服务器时间：</code></td>
                <td>
                    {{{date("Y年n月j日 H:i:s")}}}
                </td>
            </tr>
            <tr>
                <td><code>北京时间：</code></td>
                <td>
                    {{{ gmdate("Y年n月j日 H:i:s",time()+8*3600)}}}
                </td>
            </tr>
            <tr>
                <td><code>IP：</code></td>
                <td>
                    {{{gethostbyname($_SERVER['SERVER_NAME'])}}}
                </td>
            </tr>
            <tr>
                <td><code>剩余空间：</code></td>
                <td>
                    {{{ round((@disk_free_space(".")/(1024*1024)),2).'M'}}}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
</div>



</div>
@stop
