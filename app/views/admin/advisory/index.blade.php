@extends('layouts.admin_layout')

@section('title') 阅读报告管理 @stop

@section('content')
<div id="content-wrapper" xmlns="http://www.w3.org/1999/html">
<div class="page-header">
    <h1><span class="text-light-gray">用户内容 / </span>阅读报告</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">

        <div class="panel">

            <div class="panel-heading">
                <span class="panel-title">阅读报告</span>
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
<!--                <div class="panel-body">-->
<!--                    <a href="#" data-toggle="modal" data-target="#myModal"><button type="button" class="btn btn-success" style="float: right">添加单元</button></a>-->
<!--                </div>-->
            </div>
            <div class="panel-body">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>阅读课文</th>
                        <th>分析报告</th>
                        <th>生成时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($advisory as $tape)
                    <tr>
                        <td>{{{$tape->id}}}</td>
                        <td>{{{$tape->reading_id}}}</td>
                        <td>{{{$tape->path}}}</td>
                        <td>{{{$tape->created_at}}}</td>
                        <td>
                            <a href="#" class="btn btn-xs ">查看</a>
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
                    {{$advisory->appends(Input::except('page'))->links()}}
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