<!DOCTYPE html>
<html>

<head>
  <!-- Meta, title, CSS, favicons, etc. -->
  <meta charset="utf-8">
  <title>@yield('title')</title>
  <meta name="keywords" content="HTML5 Bootstrap 3 Admin Template UI Theme" />
  <meta name="description" content="AdminDesigns - A Responsive HTML5 Admin UI Framework">
  <meta name="author" content="AdminDesigns">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <base href="{{config('app.url')}}">
  
  
  <link rel='stylesheet' type='text/css' href="public/assets/admin/css/skin/font.css">

  <!-- Theme CSS -->
  <link rel="stylesheet" type="text/css" href="public/assets/admin/css/skin/theme.css">

  <link rel="stylesheet" type="text/css" href="public/css/dcalendar.picker.css">
  <!-- Favicon -->
  <link rel="shortcut icon" href="public/assets/admin/img/favicon.ico">

  <link rel="stylesheet" type="text/css" href="public/vendor/plugins/fancytree/skin-win8/ui.fancytree.min.css">

  <!-- jQuery -->
  <script src="public/vendor/jquery/jquery-1.11.1.min.js"></script>
  <script src="public/vendor/jquery/jquery_ui/jquery-ui.min.js"></script>


  <script src="public/vendor/plugins/fancytree/jquery.fancytree-all.min.js"></script>
  <script src="public/vendor/plugins/fancytree/extensions/jquery.fancytree.childcounter.js"></script>
  <script src="public/vendor/plugins/fancytree/extensions/jquery.fancytree.columnview.js"></script>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- [if lt IE 9]> -->
<!-- 
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
  <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
   -->
<!-- <![endif] -->

  <style type="text/css">
    .img_class_big {
      position:absolute; 
      width: 50%;
    }

    .img_class_small {
      width: 30px;
    }

    .panel{
      overflow-x:auto;
      position: inherit;
    }

    .panel-heading{
      border: 0px solid #e2e2e2;
    }

    .panel-body{
      border: 0px solid #e2e2e2;
    }

    body a{
      cursor: pointer;
    }

    .btn-active{
      background-color: #1D6FC4!important;
      color: #fff!important;
    }
    .btn-link:hover, .btn-link:focus, .btn-link:active{
      background-color: #1D6FC4!important;
      color: #fff!important;
    }


    .sidebar-menu li > a > span.caret, .sidebar-menu li > a.menu-open > span.caret {
        color: #858483;
        width: 100%;
        height: 35px;
        top: 0;
        right: 0;
        text-align: center;
        margin-left: 0;
        border: 0;
        padding-left: 135px;
    }

  </style>

</head>

<body class="sb-l-o{!! isset($body_style) ? ' '.$body_style : '' !!}">
  <!-- Start: Main -->
  <div id="main">

    <!-- 消息提示音 -->
     <audio id="myaudio" src="" controls="controls" hidden="true"  >
      </audio>

    <!-- Start: Header -->
    <header class="navbar navbar-fixed-top navbar-shadow">
      <div class="navbar-branding">
        <a class="navbar-brand" href="admin">
          {{ config('app.name') }}
        </a>
        <span id="toggle_sidemenu_l" class="ad ad-lines"></span>
      </div>
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown menu-merge">
          <a href="#" class="dropdown-toggle fw600 p15" data-toggle="dropdown">
          	<span class="hidden-xs pl15"> {{ session('blockchain_rz_admin_info')['true_name'] }} </span>
            <span class="caret caret-tp hidden-xs"></span>
          </a>

          <ul class="dropdown-menu list-group dropdown-persist w250" role="menu">
            
            <!-- <li class="list-group-item">
              <a href="#" class="animated animated-short fadeInUp">
                <span class="fa fa-gear"></span> 个人设置 </a>
            </li> -->

            <li class="dropdown-footer">
              <a href="admin/logout"onclick="event.preventDefault();document.getElementById('logout-form').submit();">

              <form id="logout-form" action="admin/logout" method="POST" style="display: none;">
                  {{ csrf_field() }}
              </form>
              
              <span class="fa fa-power-off pr5"></span> 退出登录 </a>
            </li>



          </ul>
        </li>
      </ul>
    </header>
    <!-- End: Header -->
    
    <!-- Start: Sidebar -->
    @include('admin.navigation')

    <!-- Start: Content-Wrapper -->
    <section id="content_wrapper">
    @yield('content')
    </section>

  </div>
  <!-- End: Main -->


  <!-- Theme Javascript -->
  <script src="public/assets/admin/js/utility.js"></script>
  <script src="public/assets/admin/js/demo.js"></script>
  <script src="public/assets/admin/js/main.js"></script>
  <script src="public/assets/admin/js/dcalendar.picker.js"></script>
  <script type="text/javascript">
    // 删除确认框
    function deleteConfirm(){
      if( confirm("确认删除？") ){
        return true;
      }
      event.preventDefault();
      event.stopPropagation();
      return false;
    }
  </script>

  <script type="text/javascript">
  jQuery(document).ready(function() {

    "use strict";

    // Init Theme Core    
    Core.init();

    var dropOpenId = $('#categoryDown').data('id');
    if (dropOpenId == undefined) {
      $('.accordion-toggle').removeClass('menu-open');
    }
    else{
      $('.firstDrop').each(function(){
         var firstDrop = $(this).data('id');
         if (dropOpenId == firstDrop) {
            // $(this).next('ul').slideUp('fast', 'swing', function() {
            //    $(this).attr('style', '').prev().removeClass('menu-open');
            // });
            var parentDrop = $(this).parents('li.dropNav');
            var siblingDrop = $(this).parent().siblings('li').children('a.accordion-toggle');
            var lastDrop = $(this).parent().parent().parent().siblings('li').children('a.accordion-toggle');
            if ($(this).hasClass('.accordion-toggle')) {
              if(parentDrop.length == 1){
                $(this).addClass('menu-open');
              }
              else{
                parentDrop.find('.accordion-toggle').addClass('menu-open');
              }
              siblingDrop.removeClass('menu-open');
            }
            else{
              if(parentDrop.length == 1){
                $(this).addClass('menu-open');
              }
              else{
                parentDrop.find('.accordion-toggle').addClass('menu-open');
              }
              siblingDrop.removeClass('menu-open');
              lastDrop.removeClass('menu-open');
            }
         }
      })
    }

  });
  </script>

  <script type="text/javascript">
    var showMenuOpen = function(){
      $('a.accordion-toggle.menu-open').next('ul').slideUp('fast', 'swing', function() {
         $(this).attr('style', '').prev().addClass('menu-open');
      });
    }

    showMenuOpen();
  </script>

  <script type="text/javascript">
    $("li.myDropNav").click(function(){
      $(".dropNav,.active").removeClass("active");
      $(this).addClass("active");
    });

    $("a.myFirstDrop").click(function(){
      // 删除同一层其他节点的active状态
      $(".mySecondDropNav,.active").removeClass("active");
      // 标记当前点击的节点为active状态
      $(this).closest('.myDropNav').attr("class", "dropNav myDropNav active");
      event.stopPropagation();
    });
  </script>

  <script type="text/javascript">
    $(function(){
      $(".firstDrop").click(function(){
        // $(this).children(".caret").trigger("click");
        // myfn(event);
      });
    });
  </script>
  <!-- END: PAGE SCRIPTS -->

</body>

</html>
