@extends('layouts.admin_layout')

@section('title') 公告 @stop
@section('content')
<div id="content-wrapper">
    <div class="page-header">
        <h1><span class="text-light-gray">系统 /公告 </span></h1>
    </div> <!-- / .page-header -->

    <div class="row">
        <div class="col-sm-12">

            <div class="panel">

                <div class="panel-heading">
                    <span class="panel-title">公告列表</span>
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
                <div class="panel">
                    <div class="panel-body">

                    </div>
                </div>
                <div class="panel-body">

                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>标题</th>
                            <th>内容</th>
                            <th>学校</th>
                            <th>班级</th>
                            <th>类型</th>
                            <th>
                                操作
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($notices as $n)
                        <tr>
                            <td>{{$n->id}}</td>
                            <td>{{{$n->title}}}</td>
                            <td>{{{$n->content}}}</td>
                            <td>{{{$n->school}}}</td>
                            <td>{{{$n->class}}}</td>
                            <td>{{{$n->type == '1' ?  '班级公告' : '学校公告'}}}</td>
                            <td>
                                <a href="{{{route('adminNoticeModify',$n->id)}}}" class="btn btn-xs">修改</a>
                                <a href="{{{route('adminNoticeDestroy',$n->id)}}}" class="btn btn-xs btn-danger">删除</a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row pagination-demo">
                <div class="col-sm-12">
                    <ul class="pagination">
                        {{$notices->appends(Input::except('page'))->links()}}
                    </ul>
                </div>
            </div>
        </div>
    </div>



</div>

@stop

