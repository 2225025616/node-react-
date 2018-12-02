@extends('admin.admin')
@section('title', '人民币流水')

@section('content')

<header id="topbar" class="ph10">

  <div class="topbar-left">
    <form action="stock/exerciselist/search" method="get">
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
          充值记录
        </span>
      </div>
      <div class="panel-body pn">
        <div   class="bs-component">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>用户</th>
                <th>请求流水号</th>
                <th>充值金额</th>
                <th>支付开始时间</th>
                <th>支付成功时间</th>
                <th>状态</th>
              </tr>
            </thead>
            <tbody>
				@if( !empty($data) )
					@foreach( $data as $key => $value )
						<tr>
							<td>{{$value->id}}</td>
							<td>{{isset($value->user) && !empty($value->user) ? $value->user->old_mobile : ''}}</td>
							<td>{{$value->request_id}}</td>
							<td>{{$value->amount}}</td>
              <td>{{$value->pay_time}}</td>
							<td>{{$value->success_time}}</td>
							<td>
                @if($value->status == 0)
                  已提交
								@elseif($value->status == 1)
                  充值中
								@elseif($value->status == 2)
                  @if(!empty($value->callback) && $value->callback->status == 2)
                    充值成功
                  @else
                    充值失败
                  @endif
								@elseif($value->status == 3)
                  充值失败
								@elseif($value->status == 4)
                  已取消
                @else
                  其他
								@endif
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