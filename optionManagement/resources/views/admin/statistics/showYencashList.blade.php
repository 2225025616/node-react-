@extends('admin.admin')
@section('title', '人民币')

@section('content')

<style type="text/css">
	.red {
		color: red;
	}
</style>

<header id="topbar" class="ph10">

  <div class="topbar-left">
    <form action="admin/statistics/yuancash/search" method="get">
      <div class="col-sm-12">
          <div class="col-sm-6">
              <input type="text" name="search" class="form-control" placeholder="请输入姓名或者手机号进行查询"
                     required="required">
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
          用户人民币对账
        </span>
      </div>
      <div class="panel-body pn">
        <div   class="bs-component">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>手机号</th>
                <th>充值总金额</th>
                <th>提现总金额</th>
                <th>购买期权花费</th>
                <th>结果</th>
              </tr>
            </thead>
            <tbody>
				@if( !empty($data) )
					@foreach( $data as $key => $value )
						<tr>
							<td>{{$value->id}}</td>
							<td>{{$value->mobile}}</td>
							<td>{{$value->recharge_total}}</td>
							<td>{{$value->withdraw_total}}</td>
							<td>{{$value->buy_total}}</td>
							<td>
								@if($value->total_result >= 0)
									正常
								@else
									<span class="red">异常</span>
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