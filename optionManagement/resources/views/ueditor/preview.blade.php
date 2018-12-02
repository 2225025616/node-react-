<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>{{$top_title}}</title>

	<style type="text/css">
		body{
			text-align:center;
			background-color: #F5F5F5;
		}

		.subhead{
			margin-left: 30px;
		}

		.container{
			background-color: #FFFFFF;
			margin-left: 10%;
			margin-right: 10%;
			margin-top: 10px;
			margin-bottom: 20px;
		}
	</style>
</head>
<body>
	<h1>{{ !empty($title) ? $title : '' }}</h1>

	<h4 class="subhead">{{ !empty($time) ? $time : '' }}</h4>

	<div class="container">
		{!! $content !!}
	</div>
</body>
</html>