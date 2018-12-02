@extends('admin.admin')
@section('title', '首页')

@section('content')


<div id="content">
  <div class="panel" id="spy3">
      <div class="panel-heading">
        <span class="panel-title">
          <span class="fa fa-table"></span>
          首页
        </span>

		<div class="pull-right hidden-xs">
           <span><a href="admin_account_create">新增</a></span>
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