@extends('admin.admin')
@section('title', '行权列表')

@section('content')


<header id="topbar" class="ph10">

  <div class="topbar-left">
    <form action="admin/stock/exerciselist/search" method="get">
      <div class="col-sm-12">
          <div class="col-sm-6">
              <input type="text" name="search" class="form-control" placeholder="请输入期权名称进行查询" value="{{isset($search) ? $search : ''}}" required="required">
          </div>
          <div class="col-sm-2">
              <button type="submit" class="btn btn-default">查询</button>
          </div>
      </div>
    </form>
  </div>

</header>

<div id="content">
  <div class="panel" id="spy3">
      <div class="panel-heading">
        <span class="panel-title">
          <span class="fa fa-table"></span>
         行权列表
        </span>
        <!-- <div class="pull-right hidden-xs">
           <span><a href="admin/stock/exerciselist">行权列表</a></span>
        </div> -->
      </div>
      <div class="panel-body pn">
        <div class="bs-component table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>公司名称</th>                
                <th>期权名称</th>
                <th>期权数量</th>
                <th>期权发放时间</th>
                <th>已行权比例</th>                
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
                  <td>{{$value['publish_time']}}</td>
                  <td>{{$value['percentage']*100}}%</td>

                  <td>
                    <a href="admin/stock/exerciselist/details?id={{$value['id']}}">查看明细</a>
                  </td>
                </tr>
              @endforeach
            @endif
            </tbody>
          </table>

          
      </div>
  </div>
</div>


@endsection