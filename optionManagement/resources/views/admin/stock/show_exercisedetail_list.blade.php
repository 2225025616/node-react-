@extends('admin.admin')
@section('title', '行权列表详情')

@section('content')



<div id="content">
  <div class="panel" id="spy3">
      <div class="panel-heading">
        <span class="panel-title">
          <span class="fa fa-table"></span>
         行权明细
        </span>
       
      </div>
      <div class="panel-body pn">
        <div class="bs-component table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>手机号</th>
                <th>真实姓名</th>
                <th>身份证号</th>
                <th>数量</th>
                <th>状态</th>

              </tr>
            </thead>
            <tbody>

            @if( !empty( $data ) )
              @foreach( $data as $value )
                <tr>

                  <td>{{$value['user']->mobile}}</td>
                  <td>{{$value['user']->truename}}</td>
                  <td>{{$value['user']->idcard}}</td>
                  <td>{{$value['stock_amount']}}</td>
                  <td>
                      @if($value->status == 1)
                        未领取
                      @elseif($value->status == 2)
                        <span >已领取</span>
                    
                      @endif
                  </td>
 
                </tr>
              @endforeach
            @endif
            </tbody>
          </table>

          
      </div>
  </div>
</div>


@endsection