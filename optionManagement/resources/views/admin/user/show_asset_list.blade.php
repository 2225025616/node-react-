@extends('admin.admin')
@section('title', '用户明细')

@section('content')

<style type="text/css">
  .red {
    color: red;
  }
</style>



<div id="content">
  <div class="panel" id="spy3">
      <div class="panel-heading">
        <span class="panel-title">
          <span class="fa fa-table"></span>
          用户明细
        </span>
      </div>
      <div class="panel-body pn">
        <div   class="bs-component">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>手机号</th>
                <th>类型</th>
                <th>余额</th>
                <th>操作</th>
              </tr>
            </thead>
            <tbody>

            <tr>
              <td>{{$data['mobile']}}</td>
              <td>人民币</td>
              <td>{{$data['balance_account']}}</td>
             
            </tr>
        @if( !empty($data['userAccountStock']) )
          @foreach( $data['userAccountStock'] as $key => $value )
            <tr>
              
              <td>{{$data['mobile']}}</td>
              <td>{{$value->stock_num}}</td>
              <td>{{$value->amount}}</td>
              <td>
                  <a href="admin/usermsg/back?id={{$value['id']}}">回购</a>
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