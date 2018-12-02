<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" /> 
	<meta name="apple-mobile-web-app-capable" content="yes" />    
	<meta name="format-detection" content="telephone=no" />  

	<title>资料验真</title>
	<base href="{{config('app.url')}}">
    <!-- jQuery -->
    <script src="public/vendor/jquery/jquery-1.11.1.min.js"></script>

    <link href="public/assets/mobile/css/scan_data_result.css" rel="stylesheet" type="text/css"/>
</head>
<body>
	
<div class="main-container">
	<div class="container">

		@if( empty($data) )
			<div class="error">
				<div class="icon">
					<img src="public/assets/mobile/img/search-failed.png">
				</div>
				<div class="message">
					<span>很遗憾，消息不存在</span>
				</div>
			</div>
		@else
			<div class="success">
				<div class="icon">
					<img src="public/assets/mobile/img/search-success.png">
				</div>
				<div class="message">
					<span>信息存在</span>
				</div>
			</div>

			<div class="contents">
				<h3>认证信息</h3>

				<div class="content">
					<div class="title">
						<span>认证类型</span>
					</div>
					<div class="data">
						<span>{{$data['validate_type']}}</span>
					</div>
				</div>
				<hr>

				<div class="content">
					<div class="title">
						<span>鉴真时间</span>
					</div>
					<div class="data">
						<span>{{$data['validate_time']}}</span>
					</div>
				</div>
				<hr>
				
				<div class="content">
					<div class="title">
						<span>认证主体</span>
					</div>
					<div class="data">
						<span>{{$data['validate_company']}}</span>
					</div>
				</div>
				<hr>

				<div class="content">
					<div class="title">
						<span>验证机构</span>
					</div>
					<div class="data">
						<span>{{$data['validate_org']}}</span>
					</div>
				</div>
				<hr>

				<div class="content">
					<div class="title">
						<span>扫描次数</span>
					</div>
					<div class="data">
						<span>{{$data['scan_times']}}</span>
					</div>
				</div>
				<hr>

				<div class="content">
					<div class="title">
						<span>验证人数</span>
					</div>
					<div class="data">
						<span>{{$data['validate_person_count']}}</span>
					</div>
				</div>
			</div>
		@endif

		<div class="footer">
			<img src="public/assets/application/img/QR-code.png">
			<div class="notice">
				<span>长按关注保全网微信公众号，获取最新区块链信息</span>
			</div>
		</div>

	</div>
</div>

</body>
</html>