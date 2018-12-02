@extends('admin.admin')
@section('title', '账户管理')

@section('content')

<div id="content">
 <h4>账户管理 - {{ !empty($data) ? '更新' : '新增' }}</h4>

  <hr class="alt short">
  <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data">
    {!! csrf_field() !!}
    @if(!empty($data))
        <div class="form-group">
            <label class="col-lg-3 control-label">ID</label>
            <div class="col-lg-8">
                <div class="bs-component">
                    <p class="form-control-static text-muted">{{$data['user_id']}}</p>
                    <input type="hidden" name="user_id" value="{{$data['user_id']}}">
                </div>
            </div>
        </div>
    @endif

    <div class="form-group">
      <label class="col-lg-3 control-label">用户名</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <input type="text" name="user_name" class="form-control" placeholder="请输入用户名，必填" value="{{ !empty($data) ? $data['user_name'] : '' }}" required="required">
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">登录密码</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <input type="password" name="password" class="form-control" placeholder="请输入登录密码，必填" value="{{ !empty($data) ? $data['password'] : '' }}" required="required">

          <input type="hidden" name="old_password" value="{{ !empty($data) ? $data['password'] : '' }}">
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">真实姓名</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <input type="text" name="true_name" class="form-control" placeholder="请输入真实姓名，必填" value="{{ !empty($data) ? $data['true_name'] : '' }}" required="required">
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">邮箱</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <input type="text" name="email" class="form-control" placeholder="请输入邮箱，选填" value="{{ !empty($data) ? $data['email'] : '' }}">
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">固定电话</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <input type="text" name="phone" class="form-control" placeholder="请输入固定电话，选填" value="{{ !empty($data) ? $data['phone'] : '' }}">
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">手机号码</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <input type="text" name="mobile" class="form-control" placeholder="请输入手机号码，选填" value="{{ !empty($data) ? $data['mobile'] : '' }}">
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">用户角色</label>
      <div class="col-lg-8">
        <div class="bs-component">
          @if( !empty( $roles ) )
            @for ($i = 0; $i < count($roles); $i++)
              <div class="checkbox col-sm-12">
                <label class="col-sm-3"><input type="radio" name="role[]" value="{{$roles[$i]['role_id']}}" 
                @if( !empty($data) ) 
                {{ in_array( $roles[$i]['role_id'], $data['roles_id_arr']) ? 'checked="checked"' : '' }} 
                @endif> {{$roles[$i]['role_name']}}</label>

                @if( $i + 1 < count($roles) )
                  <label class="col-sm-3"><input type="radio" name="role[]" value="{{$roles[++$i]['role_id']}}"
                  @if( !empty($data) ) 
                  {{ in_array( $roles[$i]['role_id'], $data['roles_id_arr']) ? 'checked="checked"' : '' }} 
                  @endif> {{$roles[$i]['role_name']}}</label>
                @endif
              </div>
            @endfor
          @endif
        </div>
      </div>
    </div>


    <div class="text-right">
      <button type="submit" class="btn btn-default ph25">提交</button>
    </div>
  </form>
</div>

@endsection