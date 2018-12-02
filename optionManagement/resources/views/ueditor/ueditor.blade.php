	<script type="text/javascript">
		var BASE_URL = "{{config('app.url')}}";
		var TOKEN = "{{ csrf_token() }}";
	</script>
    <!-- 配置文件 -->
    <script type="text/javascript" src="public/vendor/ueditor/ueditor.config.js"></script>
    <!-- 编辑器源码文件 -->
    <script type="text/javascript" src="public/vendor/ueditor/ueditor.all.js"></script>
    <!-- 实例化编辑器 -->
    <script type="text/javascript">
        var ue = UE.getEditor('container');
    </script>