@extends('admin.admin')
@section('title', '用户管理')

@section('content')

<header id="topbar" class="ph10">

  <div class="topbar-left">
    <form action="admin/user_verify/search" method="get">
      <div class="col-sm-12">
          <div class="col-sm-6">
              <input type="text" name="search" class="form-control" placeholder="请输入姓名进行查询"
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
          实名认证记录
        </span>
      </div>
      <div class="panel-body pn">
        <div   class="bs-component">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>认证姓名</th>
                <th>身份证号码</th>
                <th>插入时间</th>
                <th>完成时间</th>
                <th>认证状态</th>
                <th>操作</th>
              </tr>
            </thead>
            <tbody>
				@if( !empty($data) )
					@foreach( $data as $key => $value )
						<tr>
							<td>{{$value->id}}</td>
							<td>{{$value->truename}}</td>
							<td>{{$value->old_idcard}}</td>
							<td>{{$value->add_time}}</td>
							<td>{{$value->res_time}}</td>
							<td>
								@if($value->status == 1)
									<span class="green">认证一致</span>
								@elseif($value->status == 2)
									<span class="red">认证不一致</span>
								@elseif($value->status == 3)
									<span class="gray">无结果</span>
								@else
								    未认证
								@endif
							</td>

							<td>
								<a href="admin/user_verify/updatestatus/1?user_id={{$value->id}}">认证通过</a>
								<a href="admin/user_verify/updatestatus/2?user_id={{$value->id}}">认证拒绝</a>
								<a href="admin/user_verify/updatestatus/3?user_id={{$value->id}}">查询无果</a>
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