@extends('layouts.admin_layout')

@section('title') 创建用户 @stop
@section('content')
<div id="content-wrapper">
    <div class="page-header">
        <h1><span class="text-light-gray">用户 / </span>创建用户</h1>
    </div> <!-- / .page-header -->

    <div class="row">
        <div class="col-sm-12">

            <div class="panel">

                <div class="panel-heading">
                    <span class="panel-title">用户信息</span>
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



                    <div class="row">
                        <div class="col-sm-12">

                            <!-- 10. $FORM_EXAMPLE =============================================================================

                                            Form example
                            -->
                            <form class="panel form-horizontal"  enctype="multipart/form-data" method="post" action="{{route('adminUserPostCreate')}}">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="inputEmail2" class="col-sm-2 control-label">用户名</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="inputEmail2" name="username" placeholder="用户名" >
                                        </div>
                                    </div> <!-- / .form-group -->
                                    <div class="form-group">
                                        <label for="inputPassword" class="col-sm-2 control-label">姓名</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="inputEmail2" name="name" placeholder="姓名" >
                                        </div>
                                    </div> <!-- / .form-group -->
                                    <div class="form-group">
                                        <label for="inputPassword" class="col-sm-2 control-label">密码</label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="inputEmail2" name="password" placeholder="密码" >
                                        </div>
                                    </div> <!-- / .form-group -->
                                    <div class="form-group">
                                        <label for="inputPassword" class="col-sm-2 control-label">确认密码</label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="inputEmail2" name="re_password" placeholder="确认密码" >
                                        </div>
                                    </div> <!-- / .form-group -->
                                    <div class="form-group">
                                        <label for="inputPassword" class="col-sm-2 control-label">类型</label>
                                        <div class="col-sm-10">
                                            <select class="form-control" name="type">
                                                <option value="1">系统管理员</option>
                                                <option value="2">老师</option>
                                                <option value="3">校园平台BOSS</option>
                                            </select>
                                        </div>
                                    </div> <!-- / .form-group -->
                                    <div class="form-group">
                                        <label for="inputPassword" class="col-sm-2 control-label">所属学校</label>
                                        <div class="col-sm-10">
                                            <select class="form-control" name="school_id">
                                                <option value="0">无</option>
                                                @foreach(School::all() as $school)
                                                <option value="{{{$school->id}}}">{{{$school->name}}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> <!-- / .form-group -->

                                    <div class="form-group" style="margin-bottom: 0;">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" class="btn btn-primary">创建</button>
                                        </div>
                                    </div> <!-- / .form-group -->
                                </div>
                            </form>
                            <!-- /10. $FORM_EXAMPLE -->

                        </div>
                    </div>
            </div>
        </div>
    </div>



</div>

@stop

