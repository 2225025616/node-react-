@extends('admin.admin')
@section('title', '公司管理')

@section('content')

<div id="content">
 <h4>公司管理 - {{ !empty($data) ? '更新' : '新增' }}</h4>

  <hr class="alt short">
  <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data">
    {!! csrf_field() !!}
    @if(!empty($data))
      <div class="form-group">
          <label class="col-lg-3 control-label">ID</label>
          <div class="col-lg-8">
              <div class="bs-component">
                  <p class="form-control-static text-muted">{{$data['id']}}</p>
                  <input type="hidden" name="id" value="{{$data['id']}}">
              </div>
          </div>
      </div>
    @endif

    <div class="form-group">
      <label class="col-lg-3 control-label">公司名称</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <select name="company_id" class="form-control">
            @if( !empty( $company ) )
              @foreach($company as $key => $val)
                @if( !empty($data) && $data['company_id'] == $val->id )
                  <option value="{{$val->id}}" selected="selected">{{$val->company_name}}</option>
                @else
                  <option value="{{$val->id}}">{{$val->company_name}}</option>
                @endif
              @endforeach
            @endif
          </select>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">期权名称</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <input type="text" name="stock_name" class="form-control" placeholder="请输入期权名称，必填" value="{{ !empty($data) ? $data['stock_name'] : '' }}" required="required">
        </div>
      </div>
    </div>

     <div class="form-group">
      <label class="col-lg-3 control-label">代币名称</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <select name="token_id" class="form-control">
           
            @if( !empty( $token_name ) )
              @foreach($token_name as $key => $val)
                @if( !empty($data) && $data['token_id'] == $val->id )
                  <option value="{{$val['id']}}" selected="selected">{{$val->name}}</option>
                @else
                  <option value="{{$val['id']}}">{{$val->name}}</option>
                @endif
              @endforeach
            @endif
          </select>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">期权数量</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <input type="text" name="stock_amount" class="form-control" placeholder="请输入期权数量，必填" value="{{ !empty($data) ? $data['stock_amount'] : '' }}" required="required">
        </div>
      </div>
    </div>
    <div class="form-group">
      <label class="col-lg-3 control-label">行权价格</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <input type="text" name="exercise_price" class="form-control" placeholder="请输入行权价格，必填" value="{{ !empty($data) ? $data['exercise_price'] : '' }}" required="required">
        </div>
      </div>
    </div>

    @if(isset($data['file1']))
      <div class="form-group">
        <label class="col-lg-3 control-label">****协议</label>
        <div class="col-lg-8">
          <div class="bs-component">
              <input type="file" name="file1" class="form-control" >
          </div>
        </div>
      </div>
      <div class="form-group">
        <label class="col-lg-3 control-label">查看文件</label>
        <div class="col-lg-8">
            <div class="bs-component">
            <a href="{{$data['file1']}}" target="_Blank"><img src="public/assets/application/img/pdf.png" width="100px" height="auto"></a>
            <input type="hidden" name="old_file1" value="{{$data['file1']}}" class="form-control" >
            </div>
        </div>
    </div>
    @else
      <div class="form-group">
      <label class="col-lg-3 control-label">****协议</label>
      <div class="col-lg-8">
        <div class="bs-component">
            <input type="file" name="file1" class="form-control" required="required">
        </div>
      </div>
    </div>
    @endif

    @if(isset($data['file2']))
      <div class="form-group">
        <label class="col-lg-3 control-label">****协议</label>
        <div class="col-lg-8">
          <div class="bs-component">
              <input type="file" name="file2" class="form-control" >
          </div>
        </div>
      </div>
      <div class="form-group">
        <label class="col-lg-3 control-label">查看文件</label>
        <div class="col-lg-8">
            <div class="bs-component">
            <a href="{{$data['file2']}}" target="_Blank"><img src="public/assets/application/img/pdf.png" width="100px" height="auto"></a>
            <input type="hidden" name="old_file2" value="{{$data['file2']}}" class="form-control" >
            </div>
        </div>
    </div>
    @else
      <div class="form-group">
      <label class="col-lg-3 control-label">****协议</label>
      <div class="col-lg-8">
        <div class="bs-component">
            <input type="file" name="file2" class="form-control" required="required">
        </div>
      </div>
    </div>
    @endif


    @if(isset($data['file3']))
      <div class="form-group">
        <label class="col-lg-3 control-label">****协议</label>
        <div class="col-lg-8">
          <div class="bs-component">
              <input type="file" name="file3" class="form-control" >
          </div>
        </div>
      </div>
      <div class="form-group">
        <label class="col-lg-3 control-label">查看文件</label>
        <div class="col-lg-8">
        <div class="bs-component">
          <a href="{{$data['file3']}}" target="_Blank"><img src="public/assets/application/img/pdf.png" width="100px" height="auto"></a>
          <input type="hidden" name="old_file3" value="{{$data['file3']}}" class="form-control" >
        </div>
      </div>
    </div>
    @else
      <div class="form-group">
      <label class="col-lg-3 control-label">****协议</label>
      <div class="col-lg-8">
        <div class="bs-component">
            <input type="file" name="file3" class="form-control" required="required">
        </div>
      </div>
    </div>
    @endif
    

    


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










