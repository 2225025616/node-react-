<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" /> 
	<meta name="apple-mobile-web-app-capable" content="yes" />    
	<meta name="format-detection" content="telephone=no" />  

	<title>证书验证</title>
	<base href="{{config('app.url')}}">
    <!-- jQuery -->
    <script src="public/vendor/jquery/jquery-1.11.1.min.js"></script>

    <link href="public/assets/mobile/css/scan_data_result.css" rel="stylesheet" type="text/css"/>
    <link href="public/assets/mobile/css/hash-box.css" rel="stylesheet" type="text/css"/>
</head>
<body>
	
<div class="main-container">
	<div class="container">

		@if( empty($data) )
			<div class="error">
				<div class="hash-box">
					<img src="public/assets/mobile/img/hash-box.png">
				</div>
				<div class="error-message">
					<span>此证书未通过区块链诚信防伪认证</span>
				</div>
			</div>
		@else
			<div class="success">
				<div class="hash-box">
					<img src="public/assets/mobile/img/hash-box.png">
				</div>
				<div class="hash-content">
					<div class="hash-title">
						<span>哈希值：</span>
					</div>
					<span>{{$data['certificate_hash']}}</span>
				</div>
				<div class="message">
					<span>此证书已通过区块链诚信防伪认证</span>
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