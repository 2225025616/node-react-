@extends('admin.admin')
@section('title', '人民币流水')

@section('content')

<header id="topbar" class="ph10">

  <div class="topbar-left">
    <form action="admin/yen/withdraw/search" method="get">
      <div class="col-sm-12">
          <div class="col-sm-6">
              <input type="text" name="search" class="form-control" placeholder="请输入手机号码进行查询" value="{{isset($search) ? $search : ''}}" required="required">
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
          提现列表
        </span>
      </div>
      <div class="panel-body pn">
        <div   class="bs-component table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>用户</th>
                <th>收款人开户行</th>
                <th>收款人姓名</th>
                <th>收款人账号</th>
                <th>提现金额</th>
                <th>手续费</th>
                <th>状态</th>

                <th>备注</th>
                <th>申请时间</th>
                <th>打款人</th>
                <th>打款时间</th>
              </tr>
            </thead>
            <tbody>
      				@if( !empty($data) )
      					@foreach( $data as $key => $value )
      						<tr>
                    <td>{{$value->id}}</td>
      							<td>{{isset($value->user) && !empty($value->user) ? $value->user->old_mobile : ''}}</td>
      							<td>{{$value->bank}}</td>
      							<td>{{$value->name}}</td>
                    <td>{{$value->account}}</td>
                    <td>{{$value->amount}}</td>
                    <td>{{$value->fee}}</td>
                    <td>
                      @if($value->status == 0)
                        正在审核
                        <a href="admin/yen/withdraw/check/1?id={{$value->id}}" onclick="checkConfirm()">审核通过</a>
                        <a href="admin/yen/withdraw/cancel?id={{$value->id}}" onclick="distributeConfirm()">取消</a>
                      @elseif($value->status == 1)
                        正在处理
                        <a href="admin/yen/withdraw/transferdone?id={{$value->id}}" onclick="distributeConfirm()">打款完成</a>
                        <a href="admin/yen/withdraw/cancel?id={{$value->id}}" onclick="distributeConfirm()">取消</a>
                      @elseif($value->status == 2)
                        已完成
                      @elseif($value->status == 3)
                        已取消
                      @else
                        其他
                      @endif
                    </td>

                    <td>{{$value->remark}}</td>
                    <td>{{$value->apply_time}}</td>
                    <td>{{$value->pay_username}}</td>
      							<td>{{$value->pay_time}}</td>
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