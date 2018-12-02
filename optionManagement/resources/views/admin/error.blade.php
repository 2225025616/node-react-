@extends('admin.admin')
@section('title', '错误提示 - 后台管理')

@section('content')
<section id="content" class="pn animated fadeIn">
    <div class="center-block mt50 mw800 animated fadeIn">
      <h1 class="error-title"> Error! </h1>
      <h2 class="error-subtitle">错误信息: {!! isset($message) ? $message : '' !!}</h2>
    </div>
</section>
@endsection