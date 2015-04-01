@extends('layouts.admin_layout')

@section('title') 课文勘误 @stop
@section('content')
<div id="content-wrapper">
    <div class="page-header">
        <h1><span class="text-light-gray">教材课程 / </span>课文勘误</h1>
    </div> <!-- / .page-header -->

    <div class="row">
        <div class="col-sm-12">

            <div class="panel">

                <div class="panel-heading">
                    <span class="panel-title">课文信息</span>
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
                            <form class="panel form-horizontal"  enctype="multipart/form-data" method="post" action="{{route('adminLessonPostCorrect')}}">
                                <div class="panel-body">

                                    <div class="form-group">
                                        <label for="inputEmail2" class="col-sm-2 control-label">课文标题</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="inputEmail2" name="lesson_title" placeholder="" readonly="readonly" value="{{{$lesson->title}}}">
                                            <p class="help-block" >勘误只允许更改教材文本，其他的不允许更改</p>
                                        </div>
                                    </div> <!-- / .form-group -->

                                    <div class="form-group">
                                        <label for="asdasdas" class="col-sm-2 control-label">课文文本</label>
                                        <div class="col-sm-10">
                                            <div style="float: left">
                                                <textarea style="width: 600px;height: 300px" name="raw_content" placeholder="原文文本">{{$lesson->raw_content}}</textarea>
                                            </div>

                                            <div style="float: left;margin-left: 20px">
                                                <textarea style="width:600px;height: 300px" name="asr_content" placeholder="ASR匹配文本">{{$lesson->asr_content}}</textarea>
                                            </div>
                                            <p class="help-block" style="clear: both">1.确保两边的每行对应，行数一致</p>
                                            <p class="help-block" style="clear: both">2.左边原文文本含有特殊标记，"[[A]]"用来标记A说话人、"^"代表此行前面换行、"^^"代表此行换段、"@{{12}}"对应着右边ASR中"@{{twelve}}"</p>
                                        </div>
                                    </div> <!-- / .form-group -->

                                    <div class="form-group">
                                        <label for="inputEmail2" class="col-sm-2 control-label">Tag</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="inputEmail2" name="tag" placeholder=""  value="">
                                            <p class="help-block" >本次都修改了什么？</p>
                                        </div>
                                    </div> <!-- / .form-group -->

                                    <input type="hidden" name="lesson_id" value="{{$lesson->id}}">

                                    <div class="form-group" style="margin-bottom: 0;">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" class="btn btn-primary">勘误</button>
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
</script>
@stop
