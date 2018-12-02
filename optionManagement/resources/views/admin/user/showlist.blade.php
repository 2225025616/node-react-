@extends('admin.admin')
@section('title', '用户管理')

@section('content')

<style type="text/css">
	.red {
		color: red;
	}
</style>

<header id="topbar" class="ph10">

  <div class="topbar-left">
    <form action="admin/usermsg/search" method="get">
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
          用户信息
        </span>
      </div>
      <div class="panel-body pn">
        <div   class="bs-component">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>手机号</th>
                <th>email</th>
                <th>注册时间</th>
                <th>实名认证</th>
                <th>真实姓名</th>
                <th>操作</th>
              </tr>
            </thead>
            <tbody>
				@if( !empty($data) )
					@foreach( $data as $key => $value )
						<tr>
							<td>{{$value->id}}</td>
							<td>{{$value->mobile}}</td>
							<td>{{$value->email}}</td>
							<td>{{$value->register_time}}</td>
							<td>
								@if($value->user_verified == 1)
									验证成功
								@elseif($value->user_verified == 3)
									验证失败
								@else
								    未完成
								@endif
							</td>
							<td>{{$value->truename}}</td>
							<td>
								@if($value->forbid_login == 0)
									<a href="admin/usermsg/forbid_login/1?user_id={{$value->id}}">禁止登陆</a>
								@else
									<a href="admin/usermsg/forbid_login/0?user_id={{$value->id}}" class="red">允许登陆</a>
								@endif

								@if($value->forbid_withdraw == 0)
									<a href="admin/usermsg/forbid_withdraw/1?user_id={{$value->id}}">禁止提现</a>
								@else
									<a href="admin/usermsg/forbid_withdraw/0?user_id={{$value->id}}" class="red">允许提现</a>
								@endif

								@if($value->forbid_trade == 0)
									<a href="admin/usermsg/forbid_trade/1?user_id={{$value->id}}">禁止行权</a>
								@else
									<a href="admin/usermsg/forbid_trade/0?user_id={{$value->id}}" class="red">允许交易</a>
								@endif

								
                                 <a href="admin/usermsg/asset?id={{$value['id']}}">查看资产</a>
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