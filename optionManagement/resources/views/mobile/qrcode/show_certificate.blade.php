<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" /> 
	<meta name="apple-mobile-web-app-capable" content="yes" />    
	<meta name="format-detection" content="telephone=no" />  

	<title>{{!empty($certificate) ? $certificate['com_name'].'区块链诚信认证' : '查看证书'}}</title>
	<base href="{{config('app.url')}}">
    <!-- jQuery -->
    <script src="public/vendor/jquery/jquery-1.11.1.min.js"></script>
    <link href="public/assets/mobile/css/certificate.css" rel="stylesheet" type="text/css"/>

</head>
<body>
	
	<div class="main-container">
		<div class="certificate-img">
			<img src="{{!empty($certificate) ? $certificate['certificate_img'].'?x-oss-process=image/resize,w_828' : ''}}">
		</div>
	</div>

</body>
</html>