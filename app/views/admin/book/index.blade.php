@extends('layouts.admin_layout')

@section('title') 教材管理 @stop

@section('content')
<div id="content-wrapper">
<div class="page-header">
    <h1><span class="text-light-gray">教材管理 / </span>教材列表</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">

        <div class="panel">

            <div class="panel-heading">
                <span class="panel-title">教材列表</span>
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
                    <form class="form-inline" style="float: left" method="get" action="{{route('adminBookIndex')}}">
                        <div class="form-group">
                            <label class="sr-only" for="exampleInputEmail2">教材名称</label>
                            <input type="text" class="form-control" id="exampleInputEmail2" placeholder="教材名称" name="book_name" value="{{{$input_name}}}">
                        </div>

                        <button type="submit" class="btn btn-primary">筛选</button>
                    </form>
                    <a href="{{Route('adminBookCreate')}}"><button type="button" class="btn btn-success" style="float: right">添加教材</button></a>
                </div>
            </div>
            <div class="panel-body">
                <form method="post" action="{{{route('adminBookSort')}}}">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>排序</th>
                        <th>#</th>
                        <th>教材封面</th>
                        <th>教材名称</th>
                        <th>课文数</th>
                        <th>最新版本</th>
                        <th>状态</th>
                        <th>TAG</th>
                        <th>生成时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($bookList->count())
                    @foreach($bookList as $book)
                    <tr>
                        <td><input type="text" name="sort[{{{$book->id}}}]" value="{{$book->sort}}" style="width: 30px"></td>
                        <td>{{$book->id}}</td>
                        <td><img width="30" height="40" src="{{{$book->cover}}}"/></td>
                        <td>{{{$book->name}}}</td>
                        <td>{{{Book::find($book->id)->getLessonCount()}}}</td>
                        <td><a href="#" class="label label-info">V{{{$book->version}}}</a></td>
                        <td><a href="#" class="label {{{$book->status==0 ? 'label-warning' : 'label-primary'}}}">{{{$book->status==0 ? '未发布' : '已发布'}}}</a></td>
                        <td>{{{$book->tag}}}</td>
                        <td>{{{$book->created_at}}}</td>
                        <td>
                            <a href="{{route('adminLessonCreate',array($book->id))}}" class="btn btn-xs">添加课文</a>
                            <a href="{{route('adminLessonIndex',array($book->id))}}" class="btn btn-xs">课文列表</a>
                            <a href="#" class="btn btn-xs">历史版本</a>
                            <a href="{{route('adminBookNewVersion',array($book->id))}}" class="btn btn-xs btn-primary" onclick="return confirm('创建新版本会将原教材内的所有内容拷贝一份，确定要创建？')">创建新版本</a>
                            <a href="{{route('adminBookPublish',array($book->id))}}" class="btn btn-xs btn-danger" onclick="return confirm('一旦发布，该教材将变得不可更改，确定要发布？')">发布</a>
                            <a href="{{route('adminBookDestroy',array($book->id))}}" class="btn btn-xs btn-danger">删除</a>
                        </td>
                    </tr>
                    @endforeach
                    @endif
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
                    {{$bookList->links()}}
                </ul>
            </div>
        </div>
    </div>
</div>



</div>
@stop

