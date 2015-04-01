@extends('layouts.admin_layout')

@section('title') 创建教材 @stop
@section('content')
<div id="content-wrapper">
    <div class="page-header">
        <h1><span class="text-light-gray">教材管理 / </span>创建教材</h1>
    </div> <!-- / .page-header -->

    <div class="row">
        <div class="col-sm-12">

            <div class="panel">

                <div class="panel-heading">
                    <span class="panel-title">教材信息</span>
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
                            <form class="panel form-horizontal"  enctype="multipart/form-data" method="post" action="{{route('adminBookPostCreate')}}">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="inputEmail2" class="col-sm-2 control-label">教材名称</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="inputEmail2" name="book_name" placeholder="" value="{{ Session::get('flash_session')['book_name'] }}">
                                        </div>
                                    </div> <!-- / .form-group -->
                                    <div class="form-group">
                                        <label for="inputPassword" class="col-sm-2 control-label">主标题</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="inputPassword" name="book_title" placeholder="" value="{{ Session::get('flash_session')['book_title'] }}">
                                        </div>
                                    </div> <!-- / .form-group -->
                                    <div class="form-group">
                                        <label for="inputPassword" class="col-sm-2 control-label">副标题</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="inputPassword" name="book_subtitle" placeholder="" value="{{ Session::get('flash_session')['book_subtitle'] }}">
                                        </div>
                                    </div> <!-- / .form-group -->

                                    <div class="form-group">
                                        <label for="asdasdas" class="col-sm-2 control-label">描述</label>
                                        <div class="col-sm-10">
                                            <textarea class="form-control" name="description">{{ Session::get('flash_session')['description'] }}</textarea>
                                        </div>
                                    </div> <!-- / .form-group -->
                                    <div class="form-group">
                                        <label for="inputPassword" class="col-sm-2 control-label">出版社</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="inputPassword" placeholder="" name="publisher" value="{{ Session::get('flash_session')['publisher'] }}">
                                        </div>
                                    </div> <!-- / .form-group -->
                                    <script>
                                        init.push(function () {
                                            var options = {
                                                todayBtn: "linked",
                                                orientation: $('body').hasClass('right-to-left') ? "auto right" : 'auto auto',
                                                format: 'yyyy-mm-dd'
                                            }
                                            $('#bs-datepicker-example').datepicker(options);
                                        });
                                    </script>
                                    <div class="form-group">
                                        <label for="inputPassword" class="col-sm-2 control-label">出版时间</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="bs-datepicker-example" name="publish_time" value="{{ Session::get('flash_session')['publish_time'] }}">
                                        </div>
                                    </div> <!-- / .form-group -->
                                    <script>
                                        init.push(function () {
                                            $('#styled-finputs-example').pixelFileInput({ placeholder: 'No file selected...' });
                                        })
                                    </script>
                                    <div class="form-group">
                                        <label for="asdasdas" class="col-sm-2 control-label">封面</label>
                                        <div class="col-sm-10">
                                            <input type="file" id="styled-finputs-example" name="cover">
                                            <p class="help-block text-primary" style="clear: both">只支持JPEG、GIF、PNG格式</p>
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

