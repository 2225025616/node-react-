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
           <span><a href="admin/stock/list/save">新增</a></span>
        </div>
      </div>
      <div class="panel-body pn">
        <div class="bs-component table-responsive">
          <table class="table table-hover" >
            <thead>
              <tr>
                <th>ID</th>
                <th>公司名称</th>
                <th>期权名称</th>
                <th>总数</th>               
                <th>发行期数</th>               
                <th>创建时间</th>               
                <th>状态</th>               
             
                <th>操作</th>
              </tr>
            </thead>
            <tbody>

            @if( !empty( $data ) )
              @foreach( $data as $value )
                <tr>
                  <td>{{$value['id']}}</td>
                  <td>{{$value['company_info']->company_name}}</td>
                  <td>{{$value['stock_name']}}</td>
                  <td>{{$value['stock_amount']}}</td>
                  <td>{{$value['exercise_times']}}</td>

                  <td>{{$value['created_at']}}</td>
                  <td>
                      @if($value->status == 1)
                        未发布
                      @elseif($value->status == 2)
                        <span >已发布</span>
                    
                      @endif
                  </td>
                  
                  <td>
                    <a href="admin/stock/exercise/save?id={{$value['id']}}">行权分配</a>
                    @if($value['status'] == 1)
                      <a href="admin/stock/publish/save?id={{$value['id']}}">发行</a>
                    @endif
                    <a href="admin/stock/list/save?id={{$value['id']}}">编辑</a>
                    <a href="admin/stock/list/delete?id={{$value['id']}}">删除</a>

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