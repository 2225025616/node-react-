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
          <input type="text" name="company_name" class="form-control" placeholder="请输入公司名称，必填" value="{{ !empty($data) ? $data['company_name'] : '' }}" required="required">
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">股票代码</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <input type="text" name="stock_num" class="form-control" placeholder="请输入股票代码，必填" value="{{ !empty($data) ? $data['stock_num'] : '' }}" required="required">
        </div>
      </div>
    </div>

    @if(isset($data['business_license_img']))
      <div class="form-group">
        <label class="col-lg-3 control-label">营业执照</label>
        <div class="col-lg-8">
          <div class="bs-component">
              <input type="file" name="business_license_img" class="form-control" >
          </div>
        </div>
      </div>
      <div class="form-group">
        <label class="col-lg-3 control-label">查看图片</label>
        <div class="col-lg-8">
          <div class="bs-component">
            <img src="{{$data['business_license_img']}}" width="100px" height="auto">
            <input type="hidden" name="old_image" value="{{$data['business_license_img']}}" class="form-control" required="required">
          </div>
        </div>
      </div>
    @else
      <div class="form-group">
      <label class="col-lg-3 control-label">营业执照</label>
      <div class="col-lg-8">
        <div class="bs-component">
            <input type="file" name="business_license_img" class="form-control" required="required">
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










