@extends('admin.admin')
@section('title', '行权分配')

@section('content')

<div id="content">
 <h4>股权发行</h4>

    <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data">
        {!! csrf_field() !!}
        <div class="form-group">
        <label class="col-lg-3 control-label">人员分配表</label>
        <div class="col-lg-8">
            <div class="bs-component">
                <input type="file" name="file" class="form-control" required="required">
            </div>
        </div>
        </div>

        <div class="text-center">
            <a href="http://chengxin-public.oss-cn-shanghai.aliyuncs.com/chengxin_rz/15053821135763df705faeec6dceff01df226210c3792d11e26fe4c0cf05bd67058fad56c0e2f1.xls"><button type="button" class="btn btn-default ph25">下载模板</button></a>
            <button type="submit" class="btn btn-default ph25">提交</button>
        </div>
    </form>
</div>
<hr class="alt short">
<div class="panel-body pn">
    <div class="bs-component table-responsive">
        <table class="table table-hover" >
        <thead>
            <tr>
            
            <th>手机号</th>
            <th>姓名</th>               
            <th>身份证号</th>               
            <th>数量</th>               
            <th>操作</th>            
            </tr>
        </thead>
        <tbody>

        @if( !empty( $data ) )
            @foreach( $data as $value )
            <tr>
                
                <td>{{$value['user']->mobile}}</td>

                <td>{{$value['user']->truename}}</td>

                <td>{{$value['user']->idcard}}</td>

                <td>{{$value['stock_amount']}}</td>
                <td>                  
                    <a href="admin/stock/exercise/delete?id={{$value['id']}}">删除</a>
                </td>
            </tr>
            @endforeach
        @endif
        </tbody>
        </table>
    </div>
    <div class="text-center">
    @if( $data != "[]" )
            <a href="admin/stock/publish/update?id={{$data[0]['stock_id']}}"><button type="type" class="btn btn-default ph25">确认发放</button></a>
        @else
            <button type="type" class="btn btn-default ph25">没有添加人员，无法发放</button> 
        @endif
    </div>
    
</div>
 

@endsection










