@extends('layouts.admin_layout')

@section('title') 单元管理 @stop

@section('content')
<div id="content-wrapper" xmlns="http://www.w3.org/1999/html">
<div class="page-header">
    <h1><span class="text-light-gray">教材管理 / </span>单元列表</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">

        <div class="panel">

            <div class="panel-heading">
                <span class="panel-title">单元列表</span>
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



            <div id="modifyModal" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="post" action="{{{Route('adminUnitModify')}}}" id="unit_modify">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title" id="myModalLabel">修改单元</h4>
                            </div>
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="inputPassword" class="col-sm-2 control-label">所属教材</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="book_name" readonly="readonly" placeholder="" value=""  >
                                    </div>

                                </div> <!-- / .form-group -->

                                <div class="form-group">
                                    <label for="inputPassword" class="col-sm-2 control-label">单元名称</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="unit_name" name="unit_name" placeholder="" value="" >
                                    </div>

                                </div> <!-- / .form-group -->
                                <input type="hidden" id="unit_id" name="unit_id" value="">
                            </div> <!-- / .modal-body -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                                <button type="button" class="btn btn-primary" onclick="modifyUnit()">修改</button>
                            </div>
                        </form>
                    </div> <!-- / .modal-content -->
                </div> <!-- / .modal-dialog -->
            </div>
            <div class="panel">
                <div class="panel-body">
                    <a href="#" data-toggle="modal" data-target="#myModal"><button type="button" class="btn btn-success" style="float: right">添加单元</button></a>
                </div>
            </div>
            <div class="panel-body">
                <form method="post" action="{{{route('adminUnitMutiSort')}}}">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>排序</th>
                        <th>#</th>
                        <th>单元名称</th>
                        <th>所属教材</th>
                        <th>状态</th>
                        <th>教材版本</th>
                        <th>生成时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($units as $unit)
                    <tr>
                        <td><input type="text" name="sort[{{{$unit->id}}}]" value="{{$unit->sort}}" style="width: 30px"></td>
                        <td>{{{$unit->id}}}</td>
                        <td>{{{$unit->name}}}</td>
                        <td>{{{$unit->bookName}}}</td>
                        <td><a href="#" class="label label-warning">{{{$unit->status}}}</a></td>
                        <td><a href="#" class="label label-primary">{{{$unit->version}}}</a></td>
                        <td>{{{$unit->created_at}}}</td>
                        <td>
                            <a href="#" data-toggle="modal" data-target="#modifyModal"class="btn btn-xs" onclick="appendValues('{{{$unit->bookName}}}','{{{$unit->name}}}','{{{$unit->id}}}')"> 修改</a>
                            <a href="{{route('adminUnitDestroy',array($unit->id))}}" class="btn btn-xs btn-danger">删除</a>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                <button  class="btn btn-sm" type="submit">排序</button>
                </form>
            </div>
        </div>
        <!-- /5. $DEFAULT_TABLES -->
        <div class="row pagination-demo">
            <div class="col-sm-12">
                <ul class="pagination">
                    {{$units->appends(Input::except('page'))->links()}}
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