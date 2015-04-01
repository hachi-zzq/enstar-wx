@extends('layouts.admin_layout')

@section('title') 公告 @stop
@section('content')
<div id="content-wrapper">
    <div class="page-header">
        <h1><span class="text-light-gray">系统 / </span>公告</h1>
    </div> <!-- / .page-header -->

    <div class="row">
        <div class="col-sm-12">

            <div class="panel">

                <div class="panel-heading">
                    <span class="panel-title">公告内容</span>
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
                        <form class="panel form-horizontal"  enctype="multipart/form-data" method="post" action="{{route('adminNoticePostModify')}}">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="inputEmail2" class="col-sm-2 control-label">公告标题</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="inputEmail2" name="title" placeholder="" value="{{{$notice->title}}}" >
                                    </div>
                                </div> <!-- / .form-group -->
                                <div class="form-group">
                                    <label for="inputPassword" class="col-sm-2 control-label">公告内容</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" rows="3" name="content">{{{$notice->content}}}</textarea>
                                    </div>
                                </div> <!-- / .form-group -->

                                <div class="form-group">
                                    <label for="inputPassword" class="col-sm-2 control-label">选择班级</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="class">
                                            @if($classes->count())
                                            @foreach($classes as $c)
                                            <option value="{{{$c->id}}}" @if($c->id == $notice->class_id) selected="selected" @endif>{{{School::find($c->school_id)->name}}} {{{$c->name}}}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div> <!-- / .form-group -->
                                <input type="hidden" name="notice_id" value="{{{$notice->id}}}">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" class="btn btn-primary">修改</button>
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

