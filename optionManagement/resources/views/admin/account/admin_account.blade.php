@extends('admin.admin')
@section('title', '账户管理基本信息')

@section('content')


<div id="content">
  <div class="panel" id="spy3">
      <div class="panel-heading">
        <span class="panel-title">
          <span class="fa fa-table"></span>
          账户管理基本信息
        </span>

		<div class="pull-right hidden-xs">
           <span><a href="admin/admin_account_create">新增</a></span>
        </div>
      </div>
      <div class="panel-body pn">
        <div   class="bs-component">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>管理员ID</th>
                <th>用户名</th>
                <th>真实姓名</th>
                <th>是否被锁定</th>
                <th>所属角色</th>
                <th>操作</th>
              </tr>
            </thead>
            <tbody>
				@if( !empty($data) )
					@foreach( $data as $key => $value )
						<tr>
							<td>{{$value->user_id}}</td>
							<td>{{$value->user_name}}</td>
							<td>{{$value->true_name}}</td>
							<td>
								{{$value->locked > 0 ? '是' : '否'}}
							</td>
							<td>{{$value->admin_roles_val}}</td>
							<td>
								<a href="admin/admin_account_update?id={{$value->user_id}}">编辑</a>
								@if( $value->locked == 0 )
									<a href="admin/admin_account_disable?id={{$value->user_id}}">锁定</a>
								@elseif( $value->locked == 1 )
									<a href="admin/admin_account_enable?id={{$value->user_id}}">解除锁定</a>
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