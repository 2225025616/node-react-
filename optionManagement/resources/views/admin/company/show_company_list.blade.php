@extends('admin.admin')
@section('title', '项目列表')

@section('content')

<div id="content">
  <div class="panel" id="spy3">
      <div class="panel-heading">
        <span class="panel-title">
          <span class="fa fa-table"></span>
          项目列表
        </span>
        <div class="pull-right hidden-xs">
           <span><a href="admin/company/category/save">新增</a></span>
        </div>
      </div>
      <div class="panel-body pn">
        <div class="bs-component table-responsive">
          <table class="table table-hover" >
            <thead>
              <tr>
                <th>ID</th>
                <th>公司名称</th>
                <th>公司股票代码</th>
                <th>提交时间</th>               
                              
                <th>操作</th>
              </tr>
            </thead>
            <tbody>

            @if( !empty( $data ) )
              @foreach( $data as $value )
                <tr>
                  <td>{{$value['id']}}</td>
                  <td>{{$value['company_name']}}</td>
                  <td>{{$value['stock_num']}}</td>
                  <td>{{$value['created_at']}}</td>
                  <td>
                    <!-- <a data-toggle="modal" data-target="#myModal_{{$value->id}}">查看内容</a> -->
                    <a href="admin/company/category/save?id={{$value['id']}}">编辑</a>
                    <a href="admin/company/category/delete?id={{$value['id']}}">删除</a>

                  </td>
                </tr>
              @endforeach
            @endif
            </tbody>
          </table>

          @if( !empty( $data ) )
            <div class="text-right">
              <?php echo $data->render(); ?>
            </div>
          @endif
      </div>
  </div>
</div>


@endsection