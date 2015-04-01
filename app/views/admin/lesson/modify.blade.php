@extends('layouts.admin_layout')

@section('title') 修改课文 @stop

<link rel=stylesheet href="/codemirror/lib/codemirror.css">

<script src="/codemirror/lib/codemirror.js"></script>
<script src="/codemirror/mode/xml/xml.js"></script>
<script src="/codemirror/mode/javascript/javascript.js"></script>
<script src="/codemirror/mode/css/css.js"></script>
<script src="/codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="/codemirror/addon/edit/matchbrackets.js"></script>

<script src="doc/activebookmark.js"></script>

<style type="text/css">
    .CodeMirror{
        border: 1px solid #ccc;
        width: 1470px;
        height: 400px;
        font-family: consolas, monospace;
        line-height: 1.5em;
        font-size: 13px;;
    }
</style>

@section('content')
<div id="content-wrapper">
    <div class="page-header">
        <h1><span class="text-light-gray">教材课程 / </span>修改课文</h1>
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


                <!-- Modal -->
                <div id="myModal" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="post" action="{{{Route('adminUnitCreate')}}}" id="unit_create">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title" id="myModalLabel">添加单元</h4>
                            </div>
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="inputPassword" class="col-sm-2 control-label">所属教材</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="book" id="jq-validation-select2">
                                            @foreach(Book::all() as $book)
                                            <option value="{{$book->id}}">{{$book->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div> <!-- / .form-group -->

                                <div class="form-group">
                                    <label for="inputPassword" class="col-sm-2 control-label">单元名称</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="inputEmail2" name="unit_name" placeholder=""  >
                                    </div>

                                </div> <!-- / .form-group -->

                            </div> <!-- / .modal-body -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                                <button type="button" class="btn btn-primary" onclick="createUnit()">保存</button>
                            </div>
                            </form>
                        </div> <!-- / .modal-content -->
                    </div> <!-- / .modal-dialog -->
                </div> <!-- /.modal -->

                    <div class="row">
                        <div class="col-sm-12">

                            <!-- 10. $FORM_EXAMPLE =============================================================================

                                            Form example
                            -->
                            <form class="panel form-horizontal"  enctype="multipart/form-data" method="post" action="{{route('adminLessonPostModify')}}">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="inputPassword" class="col-sm-2 control-label">所属教材/单元</label>
                                        <div class="col-sm-10">
                                            <select class="form-control" name="book_unit" id="jq-validation-select2">

                                                @foreach(Book::getBookLessonTree() as $v)
                                                <option value="book_{{$v['id']}}" style="font-weight: bolder"@if($v['id']==$book_id and $unit_id == null) selected='selected' @endif>{{$v['name']}}</option>
                                                    @if(!empty($v['units']))
                                                    @foreach($v['units'] as $v_unit)
                                                <option value="book_{{$v['id']}}unit_{{$v_unit['id']}}" style="color: #aaa;padding-left: 10px" @if($v_unit['id']==$unit_id) selected='selected' @endif>|--{{$v_unit['name']}}</option>
                                                    @endforeach
                                                    @endif
                                                @endforeach
                                            </select>
                                            <p class="help-block" style="clear: both">选择将要添加的课文的位置，教材或者单元</p>
                                        </div>

                                    </div> <!-- / .form-group -->

                                    <div class="form-group">
                                        <label for="inputPassword" class="col-sm-2 control-label"></label>
                                        <div class="col-sm-10">
                                            <a href="#" data-toggle="modal" data-target="#myModal">添加单元</a>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail2" class="col-sm-2 control-label">课文标题</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="inputEmail2" name="lesson_title" placeholder="" value="{{$lesson->title}}">
                                        </div>
                                    </div> <!-- / .form-group -->
                                    <script>
                                        init.push(function () {
                                            $('#jq-validation-select2').select2({ allowClear: true, placeholder: 'Select a element...' }).change(function(){
                                                $(this).valid();
                                            });
                                        });
                                    </script>
                                    <script>
                                        init.push(function () {
                                            $('#styled-finputs-example').pixelFileInput({ placeholder: 'No file selected...' });
                                        })
                                    </script>
                                    <div class="form-group">
                                        <label for="asdasdas" class="col-sm-2 control-label">课文标准音</label>
                                        <div class="col-sm-10">
                                            <input type="file" id="styled-finputs-example" name="audio">
                                            <p class="help-block" style="clear: both">{{$lesson->audio}}</p>
                                            <p class="help-block" style="clear: both">只支持MP3、WAV、M4A格式</p>
                                        </div>
                                    </div> <!-- / .form-group -->

                                    <div class="form-group">
                                        <label for="asdasdas" class="col-sm-2 control-label">课文文本</label>
                                        <div class="col-sm-10">
                                            <div style="float: left">
<!--                                                <textarea style="width: 600px;height: 300px" name="raw_content" placeholder="原文文本"></textarea>-->
                                                <textarea id=demotext name="raw_content" placeholder="原文文本">{{$lesson->raw_content}}</textarea>
                                            </div>

                                            <div style="float: left;margin-top: 5px">
<!--                                                <textarea style="width:600px;height: 300px" name="asr_content" placeholder="ASR匹配文本"></textarea>-->
                                                <textarea id=demotext_2 name="asr_content" placeholder="ASR匹配文本">{{$lesson->asr_content}}</textarea>
                                            </div>
                                            <p class="help-block" style="clear: both">1.确保两边的每行对应，行数一致</p>
                                            <p class="help-block" style="clear: both">2.左边原文文本含有特殊标记，"[[A]]"用来标记A说话人、"^"代表此行前面换行、"^^"代表此行换段、"{12}"对应着右边ASR中"{twelve}"</p>
                                        </div>
                                    </div> <!-- / .form-group -->

                                    <input type="hidden" name="lesson_id" value="{{$lesson->id}}">

                                    <div class="form-group" style="margin-bottom: 0;">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" class="btn btn-primary">修改</button>
                                        </div>
                                    </div> <!-- / .form-group -->
                                </div>
                            </form>
                            <!-- /10. $FORM_EXAMPLE -->
                            <script>
                                var editor = CodeMirror.fromTextArea(document.getElementById("demotext"), {
                                    lineNumbers: true,
                                    mode: "text/html",
                                    matchBrackets: true
                                });
                                var editor = CodeMirror.fromTextArea(document.getElementById("demotext_2"), {
                                    lineNumbers: true,
                                    mode: "text/html",
                                    matchBrackets: true
                                });
                            </script>
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
