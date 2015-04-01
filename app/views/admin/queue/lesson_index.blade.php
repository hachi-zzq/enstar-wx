@extends('layouts.admin_layout')

@section('title')课文分析队列 @stop

@section('content')
<div id="content-wrapper" xmlns="http://www.w3.org/1999/html">
<div class="page-header">
    <h1><span class="text-light-gray">队列 / </span>课文分析队列</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <div class="panel">

            <div class="panel-heading">
                <p class="text-success" >队列KEY: {{$key}}</p>
                <p class="text-success" >当前长度: {{$len}}</p>
                <a href="{{route('flushKey',array('lesson'))}}"  onclick="return confirm('清空后，不可恢复，确定？')"><button type="button" class="btn btn-danger" style="float: right;margin-right: 10px;" >清空该队列</button></a>
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
                    };
                    $('#start_time').datepicker(options);
                    $('#end_time').datepicker(options);
                })
            </script>

            <div class="panel-body">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>index</th>
                        <th>内容</th>
                        <th>提交时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($list as $k=>$l)
                    <tr>
                        <td>{{$k}}</td>
                        <td>{{$l}}</td>
                        <td></td>
                        <td>
                            <a href="#" class="btn btn-xs">具体查看</a>
                            <a href="#" class="btn btn-xs btn-danger" onclick="return confirm('确定要删除？')">删除</a>
                        </td>
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
                </ul>
            </div>
        </div>
    </div>
</div>



</div>
@stop
