


@extends('admin.admin')
@section('title', '人民币')

@section('content')

<style type="text/css">
	.row span{
    line-height: 35px;
	}
</style>

<header id="topbar" class="ph10">

  <div class="topbar-left col-sm-3">
    <ul class="nav nav-list nav-list-topbar pull-left">
      <li><a href="admin/statistics/yuanstat?status=1" class="btn btn-link {{($status==1)?'btn-active':''}}" style="color: #1D6FC4;">充值记录</a></li>
      <li><a href="admin/statistics/yuanstat?status=2" class="btn btn-link {{($status==2)?'btn-active':''}}" style="color: #1D6FC4;">提现记录</a></li>
    </ul>
  </div>

  <div class="row col-sm-9">
    <form action="admin/statistics/yuanstat/search/{{$status}}">
      <div class="col-sm-10">
        <span class="col-sm-2">起始日期</span>
        <div class="col-sm-4">
          <input type="date" name="start" class="form-control" value="{{isset($start) ? $start : ''}}" required="required">
        </div>

        <span class="col-sm-2">截止日期</span>
        <div class="col-sm-4">
          <input type="date" name="end" class="form-control" value="{{isset($end) ? $end : ''}}" required="required">
        </div>

      </div>

      <div class="col-sm-2">
        <button type="submit" class="btn btn-default" type="button">查询</button>
      </div>
    </form>
  </div>
</header>


<div id="content">
  <div class="panel" id="spy3">
      <div class="panel-heading">
        <span class="panel-title">
          <span class="fa fa-table"></span>
          人民币统计
        </span>
      </div>
      <div class="panel-body pn">
        <div   class="bs-component">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>手机号</th>
                <th>金额</th>
                <th>类型</th>
                <th>创建时间</th>
              </tr>
            </thead>
            <tbody>
      				@if( isset($data) )
      					@foreach( $data as $key => $value )
      						<tr>
      							<td>{{$value->id}}</td>
      							<td>{{isset($value->user) && !empty($value->user) ? $value->user->mobile : ''}}</td>
                    <td>{{$value->value}}</td>
      							<td>{{$value->description}}</td>
      							<td>{{$value->create_time}}</td>
      						</tr>
      					@endforeach
      				@endif

              <tr class="red">
                <td colspan="2">合计</td>
                <td colspan="3">{{$total_yen}}</td>
              </tr>
            </tbody>
          </table>

          @if( !empty( $data ) )
	          <div class="text-right">
      				<?php echo $data->appends(['start' => isset($start) ? $start : '', 'end' => isset($end) ? $end : ''])->render(); ?>
	          </div>
          @endif
      </div>
  </div>
</div>



@endsection