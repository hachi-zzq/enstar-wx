@extends('layouts.admin_layout')

@section('title') 课文管理 @stop

@section('content')
<div id="content-wrapper" xmlns="http://www.w3.org/1999/html">
<div class="page-header">
    <h1><span class="text-light-gray">教材课程 / </span>课文列表</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">

        <div class="panel">

            <div class="panel-heading">
                <span class="panel-title">课文列表</span>
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
                    <form class="form-inline" style="float: left" method="get" action=" ">
                        <div class="form-group">
                            <label class="sr-only" for="exampleInputEmail2">教材名称</label>
                            <select class="form-control" name="book_id" id="jq-validation-select2" style="width: 200px">
                                @foreach(Book::all() as $book)
                                <option value="{{$book->id}}" @if($book_id == $book->id) selected='selected' @endif >{{$book->name}} V({{$book->version}})</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">筛选</button>
                    </form>
                    <a href="{{route('adminBookPublish',array($book_id))}}" onclick="return confirm('一旦发布，该教材将变得不可更改，确定要发布？')"><button type="button" class="btn btn-danger" style="float: right;margin-left: 20px">发布该教材</button></a>
                    <a href="{{{route('adminLessonCreate',array('book_id'=>$book_id))}}}"><button type="button" class="btn btn-success" style="float: right">添加课文</button></a>


                </div>
            </div>
            <div class="panel-body">
                @if($units)
                @foreach($units as $unit)
                <div class="table-light">
                    <div class="table-header">
                        <div class="table-caption">
                            <span style="color: #1a7ab9">{{$unit->name}}</span>
                        </div>
                    </div>
                    <form method="post" action="{{{route('adminLessonMutiSort')}}}">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>排序</th>
                            <th>#</th>
                            <th>课文标题</th>
                            <th>音频地址</th>
                            <th>最新版本</th>
                            <th>当前版本</th>
                            <th>TAG</th>
                            <th>匹配状态</th>
                            <th>匹配用时</th>
                            <th>发布状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($unit->lessons as $l)
                        <tr>
                            <td><input type="text" name="sort[{{{$l->id}}}]" value="{{$l->sort}}" style="width: 30px"></td>
                            <td>{{$l->id}}</td>
                            <td>{{$l->title}}</td>
                            <td>{{$l->audio}}</td>
                            <td>{{$l->lastVersion}}</td>
                            <td>{{$l->version}}</td>
                            <td>{{$l->tag}}</td>
                            <td><a href="#" class="label {{{$l->statusClass}}}">{{{$l->status}}}</a></td>
                            <td>{{{$l->asr_duration or '无'}}}</td>
                            <td><a href="#" class="label {{{Book::find($l->book_id)->status == 0 ? 'label-warning' : 'label-primary'}}}">{{{Book::find($l->book_id)->status == 1 ? '已发布' : '未发布'}}}</a></td>
                            <td>
                                <a href="{{route('adminLessonDetail',array($l->id))}}" class="btn btn-xs">查看</a>
                                <a href="{{{route('adminLessonCorrect',array($l->id))}}}" class="btn btn-xs">勘误</a>
                                <a href="{{route('adminLessonModify',array($l->id))}}" class="btn btn-xs @if(Book::find($l->book_id)->status == 1) disabled @endif">修改</a>
                                <a href="{{{route('adminLessonRehash',array($l->id,Input::get('page')))}}}" class="btn btn-xs btn-primary">重新分析</a>
                                <a href="#" class="btn btn-xs">历史版本</a>
                                <a href="{{route('adminLessonDestroy',array($l->id))}}" class="btn btn-xs btn-danger">删除</a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                        <input type="hidden" name="book_id" value="{{{$unit->book_id}}}">
                    <button  class="btn btn-sm" type="submit">排序</button>
                </div>
                @endforeach
                <div class="row pagination-demo">
                    <div class="col-sm-12">
                        <ul class="pagination">
                            {{$units->appends(Input::except('page'))->links()}}
                        </ul>
                    </div>
                </div>
                @endif

                @if($lessons)
<!--                #无单元模式-->
                <form method="post" action="{{{route('adminLessonMutiSort')}}}">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>排序</th>
                        <th>#</th>
                        <th>课文标题</th>
                        <th>音频地址</th>
                        <th>最新版本</th>
                        <th>当前版本</th>
                        <th>TAG</th>
                        <th>匹配状态</th>
                        <th>匹配用时</th>
                        <th>发布状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($lessons as $l)
                    <tr>
                        <td><input type="text" name="sort[{{{$l->id}}}]" value="{{$l->sort}}" style="width: 30px"></td>
                        <td>{{$l->id}}</td>
                        <td>{{$l->title}}</td>
                        <td>{{$l->audio}}</td>
                        <td>{{$l->lastVersion}}</td>
                        <td>{{$l->version}}</td>
                        <td>{{$l->tag}}</td>
                        <td><a href="#" class="label {{{$l->statusClass}}}">{{{$l->status}}}</a></td>
                        <td>{{{$l->asr_duration or '无'}}}</td>
                        <td><a href="#" class="label {{{Book::find($l->book_id)->status == 0 ? 'label-warning' : 'label-primary'}}}">{{{Book::find($l->book_id)->status == 1 ? '已发布' : '未发布'}}}</a></td>
                        <td>
                            <a href="{{route('adminLessonDetail',array($l->id))}}" class="btn btn-xs">查看</a>
                            <a href="{{{route('adminLessonCorrect',array($l->id))}}}" class="btn btn-xs">勘误</a>
                            <a href="{{route('adminLessonModify',array($l->id))}}" class="btn btn-xs @if(Book::find($l->book_id)->status == 1) disabled @endif">修改</a>
                            <a href="{{{route('adminLessonRehash',array($l->id,Input::get('page')))}}}" class="btn btn-xs btn-primary">重新分析</a>
                            <a href="#" class="btn btn-xs">历史版本</a>
                            <a href="{{route('adminLessonDestroy',array($l->id))}}" class="btn btn-xs btn-danger">删除</a>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                 <input type="hidden" name="book_id" value="{{{$book_id}}}">
                <button href="" class="btn btn-sm" type="submit">排序</button>
                </form>
                <!-- /5. $DEFAULT_TABLES -->
                <div class="row pagination-demo">
                    <div class="col-sm-12">
                        <ul class="pagination">
                            {{$lessons->appends(Input::except('page'))->links()}}

                        </ul>
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>
</div>



</div>
@stop

