<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" /> 
	<meta name="apple-mobile-web-app-capable" content="yes" />    
	<meta name="format-detection" content="telephone=no" />  

	<title>{{isset($certificate['certificate']['no']) ? $certificate['certificate']['no'] : '查看证书'}}</title>
	<base href="{{config('app.url')}}">
    <!-- jQuery -->
    <script src="public/vendor/jquery/jquery-1.11.1.min.js"></script>
    <link href="public/assets/mobile/css/certificate-detail.css" rel="stylesheet" type="text/css"/>

</head>
<body>
	
	<div class="main-container">
		<div class="top-message">
			<div class="message-img">
				<img src="public/assets/mobile/img/grade.png">
			</div>

			<div class="message-content">
				<p>认证评分说明</p>
			</div>
		</div>

		<div class="details">

			@if (!empty($score_cate))
				@foreach ($score_cate as $key => $cate)
					<div class="level-1 display">
						<div class="title"><span>{{$cate['name']}}</span></div>
						<div class="contents ">

							<div class="content">
								<div class="content-title">评分细则</div>
								<div class="content-value">得分分值</div>
							</div>

							@if (isset($cate['children']))
                                @foreach ($cate['children'] as $k => $second_cate)
                            	    @if(isset($certificate_data[$second_cate['score_cate_id']]))
										
										@if( !empty($certificate_data[$second_cate['score_cate_id']]['score_data']) )
                                            @foreach( $certificate_data[$second_cate['score_cate_id']]['score_data'] as $score_data )
												<div class="content uploaded">
													<div class="content-title gray">{{$second_cate['name']}}</div>
													<div class="content-value gray">{{$score_data['score_value']}}</div>
												</div>
											@endforeach
										@endif

									@endif
								@endforeach
							@endif

						</div>
					</div>
				@endforeach
			@endif

		</div>

		<div class="footer">
			<img src="public/assets/application/img/QR-code.png">
			<div class="notice">
				<span>长按关注保全网微信公众号，获取最新区块链信息</span>
			</div>
		</div>

	</div>

<script type="text/javascript">
	$(function(){
        $(".uploaded").parent().parent().removeClass('display');
    });
</script>
</body>
</html>