@extends('admin.admin')
@section('title', '角色管理')

@section('content')

<style type="text/css">
	.row{
		margin-left: 123px;
	    margin-right: -11px;
	}
</style>
<div id="content">
  <div class="panel" id="spy3">
      <div class="panel-heading">
        <span class="panel-title">
          <span class="fa fa-table"></span>
          角色管理
        </span>

		<div class="pull-right hidden-xs">
           <span><a href="admin/admin_roles_create">新增</a></span>
        </div>
      </div>
      <div class="panel-body pn">
        <div   class="bs-component">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>角色ID</th>
                <th>名称</th>
                <th>描述</th>
                <th>操作</th>
              </tr>
            </thead>
            <tbody>
				@if( !empty($data) )
					@foreach( $data as $key => $value )
						<tr>
							<td>{{$value->role_id}}</td>
							<td>{{$value->role_name}}</td>
							<td>{{$value->description}}</td>
							<td>
								<a data-toggle="modal" data-target="#myModal_{{$key}}">查看更多</a>
								<a href="admin/admin_roles_update?id={{$value->role_id}}">编辑</a>
								@if( $value->locked == 0 )
									<a href="admin/admin_roles_enable?id={{$value->role_id}}">启用</a>
								@elseif( $value->locked == 1 )
									<a href="admin/admin_roles_disable?id={{$value->role_id}}">禁用</a>
								@endif
							</td>
						</tr>

						<div id="myModal_{{$key}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
						  <div class="modal-dialog modal-lg" role="document">
						    <div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							        <h4 class="modal-title" id="gridSystemModalLabel_{{$key}}">角色拥有的权限</h4>
							      </div>
							      <div class="modal-body">
							      	<div class="row">

									@if( !empty( $value->modules ) )
										@foreach( $value->modules as $k => $val )
								          <div class="col-sm-9">
								            {{$val['module_name']}}
								            @if( !empty( $val['child'] ) )
								            	@foreach( $val['child'] as $kk => $child )
										            <div class="row">
										              <div class="col-sm-12">
										                {{$child['module_name']}}
										              </div>
										            </div>
										        @endforeach
									        @endif
								          </div>
								        @endforeach
							        @endif

							        </div>
							      </div>

						    </div>
						  </div>
						</div>
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