@extends('layouts.admin_layout')

@section('title') 用户管理 @stop

@section('content')
<div id="content-wrapper">
    <div class="page-header">
        <h1><span class="text-light-gray">用户 / </span>全部用户</h1>
    </div> <!-- / .page-header -->

    <div class="row">
        <div class="col-sm-12">

            <div class="panel">

                <div class="panel-heading">
                    <span class="panel-title">用户列表</span>
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

                <div class="panel-body">

                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>OpenID</th>
                            <th>昵称</th>
                            <th>性别</th>
                            <th>地址</th>
                            <th>关注时间</th>
                            <th>操作</th>
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
                                <td>
                                    <a href="/admin/reading/index?user_id={{{$user->id}}}" class="btn btn-xs">查看阅读记录</a>
                                </td>
                            </tr>
                        @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /5. $DEFAULT_TABLES -->
            <div class="row pagination-demo">
                <div class="col-sm-12">
                    <ul class="pagination">
                        {{$users->appends(Input::except('page'))->links()}}
                    </ul>
                </div>
            </div>
        </div>
    </div>



</div>
@stop

