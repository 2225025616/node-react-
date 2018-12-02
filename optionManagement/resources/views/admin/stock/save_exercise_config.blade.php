@extends('admin.admin')
@section('title', '行权分配')

@section('content')

<div id="content">
 <h4>行权分配</h4>

  <hr class="alt short">
  <div class="panel-body pn">
        <div class="bs-component table-responsive">
          <table class="table table-hover" >
            <thead>
              <tr>
                
                <th>期权比例</th>
                <th>行权开始日</th>               
                <th>行权截止日</th>               
                <th>存续期限</th>               
                          
                <th>操作</th>
              </tr>
            </thead>
            <tbody>

            @if( !empty( $data ) )
              @foreach( $data as $value )
                <tr>
                 
                  <td>{{$value['exercise_percentage']}}%</td>

                  <td>{{$value['start_time']}}</td>

                  <td>{{$value['end_time']}}</td>

                  <td>{{$value['duration']}}</td>
                  <td>                  
                    <a href="admin/stock/exercise/delete?id={{$value['id']}}">删除</a>
                  </td>
                </tr>
              @endforeach
            @endif
            </tbody>
          </table>

        
      </div>
    <br>
    <br>
    <br>
    <br>
  <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data">
    {!! csrf_field() !!}
   

    <div class="form-group">
      <label class="col-lg-3 control-label">期权比例</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <input type="text" name="exercise_percentage" class="form-control" placeholder="请输入期权比例，必填" value="" required="required">
        </div>
      </div>
    </div>
    <div class="form-group">
      <label class="col-lg-3 control-label">行权起始日</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <input id='mydatepicker2' class="form-control" type='text' name="start_time"  placeholder="请输入ICO开始时间，必填"   required="required"/>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">行权截止日</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <input id="mydatepicker" type="text" name="end_time" class="form-control" placeholder="请输入ICO结束时间，非必填" required="required" >
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">存续期限</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <input id="duration" type="text" name="duration" class="form-control" placeholder="0"  required="required">
        </div>
      </div>
    </div>
   
    

    


    <div class="text-right">
      <button type="submit" class="btn btn-default ph25">提交</button>
    </div>
  </form>
</div>

<script src="public/assets/admin/js/dcalendar.picker.js"></script>
<script type="text/javascript"> 
  $('#mydatepicker2').dcalendarpicker({
    format:'yyyy-mm-dd'
  });  
  $('#mydatepicker').dcalendarpicker({
    format:'yyyy-mm-dd'
  });

  
</script>

@endsection










