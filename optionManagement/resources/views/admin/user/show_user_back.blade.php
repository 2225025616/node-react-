@extends('admin.admin')
@section('title', '用户资产')

@section('content')

<div id="content">
 <h4>期权回购</h4>

  <hr class="alt short">
  <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data">
    {!! csrf_field() !!}
    @if(!empty($data))
      <div class="form-group">
          <label class="col-lg-3 control-label">用户总股数</label>
          <div class="col-lg-8">
              <div class="bs-component">
                  <p class="form-control-static text-muted">{{$data['amount']}}</p>
                  <input type="hidden" name="id" value="{{$data['id']}}">
                  <input type="hidden" name="user_id" value="{{$data['user_id']}}">
                  <input type="hidden" name="stock_num" value="{{$data['stock_num']}}">
                  
              </div>
          </div>
          <div class="text-center">
            <button  id="all-back" type="button" class="btn btn-default ph25">全部回购</button>
          </div>
      </div>
    @endif

    <div class="form-group">
      <label class="col-lg-3 control-label">回购数量</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <input type="text" name="amount" class="form-control" placeholder="请输入回购数量，必填" value="" required="required">
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">回购单价</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <input type="text" name="price" class="form-control" placeholder="请输入回购单价，必填" value="" required="required">
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">回购总价</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <input type="text" name="total_price" class="form-control" placeholder="请输入回购总价，必填" value="" required="required" readonly="true">
        </div>
      </div>
    </div>
    
    <div class="form-group">
      <label class="col-lg-3 control-label">回购说明</label>
      <div class="col-lg-8">
        <div class="bs-component">

          <select name="status" class="form-control">
           
            <option value="0" select="selected">违规操作</option>

            <option value="1">离职</option>
           

        
          </select>

        </div>
      </div>
    </div>

<<<<<<< HEAD
=======
    <div class="form-group">
      <label class="col-lg-3 control-label">详细说明</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <!-- 加载编辑器的容器 -->
        
          <script id="container" name="remark" type="file"></script>
         

          @include('ueditor.ueditor')
        </div>
      </div>
    </div>

>>>>>>> 2fefd66077d10052f4918e05da320098b32b91ba
    <div class="text-right">
      <button type="submit" class="btn btn-default ph25">提交</button>
    </div>
  </form>
</div>

<script type="text/javascript"> 

  $('#all-back').click(function(){
    var amount = '{{$data['amount']}}';
    $("input[name='amount']").val(amount);
  }); 

  $("input[name='price']").blur(function(){
    var price = $("input[name='price']").val();
    var amount = $("input[name='amount']").val();
    $("input[name='total_price']").val(price * amount);
  });

  $("input[name='amount']").blur(function(){
    var price = $("input[name='price']").val();
    var amount = $("input[name='amount']").val();
    $("input[name='total_price']").val(price * amount);
  });
 
</script>

@endsection










