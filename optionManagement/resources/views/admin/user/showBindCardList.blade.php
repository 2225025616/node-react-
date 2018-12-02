@extends('admin.admin')
@section('title', '用户管理')

@section('content')

<header id="topbar" class="ph10">

  <div class="topbar-left">
    <form action="admin/user_bindcard/search" method="get">
      <div class="col-sm-12">
          <div class="col-sm-6">
              <input type="text" name="search" class="form-control" placeholder="请输入手机号进行查询" value="{{isset($search) ? $search : ''}}" required="required">
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
          绑定银行卡
        </span>
      </div>
      <div class="panel-body pn">
        <div class="bs-component">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>申请人</th>
                <th>银行卡号</th>
                <th>开户行</th>
                <th>持卡人姓名</th>
                <th>持卡人身份证号码</th>
                <th>预留手机号</th>
                <th>状态</th>
                <th>操作</th>
              </tr>
            </thead>
            <tbody>
				@if( !empty($data) )
					@foreach( $data as $key => $value )
						<tr>
							<td>{{$value->id}}</td>
							<td>{{!empty($value->user) ? $value->user->mobile : ''}}</td>
							<td>{{$value->card_no}}</td>
							<td>{{$value->bank}}</td>
							<td>{{$value->acc_name}}</td>
							<td>{{$value->acc_id}}</td>
							<td>{{$value->mobile}}</td>
							<td>
								@if($value->status == 0)
									待验证
								@elseif($value->status == 1)
									验证中
								@elseif($value->status == 2)
									验证成功
								@elseif($value->status == 3)
									验证失败
								@endif
							</td>
							
							<td>
								<a href="admin/user_bindcard/check/2?id={{$value->id}}" onclick="distributeConfirm()">通过</a>
								<a href="admin/user_bindcard/check/3?id={{$value->id}}" onclick="distributeConfirm()">拒绝</a>
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