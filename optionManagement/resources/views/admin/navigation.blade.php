  <aside id="sidebar_left" class="nano nano-light">

    <!-- Start: Sidebar Left Content -->
    <div class="sidebar-left-content nano-content">

      <!-- Start: Sidebar Menu -->
      <ul class="nav sidebar-menu">


        <li class="sidebar-label pt20">菜单</li>
  
        <!-- 公司管理 -->
        @if( strpos(Route::getCurrentRoute()->uri(), 'home') !== false || strpos(Route::getCurrentRoute()->uri(), 'banner') !== false || strpos(Route::getCurrentRoute()->uri(), 'information') !== false )
          <li class="dropNav myDropNav active">
            <a class="firstDrop accordion-toggle menu-open">
        @else
          <li class="dropNav myDropNav">
            <a class="firstDrop accordion-toggle">
        @endif
              <span class="glyphicon glyphicon-home"></span>
              <span class="sidebar-title">公司管理</span>
              <span class="caret"></span>
            </a>

            @if( strpos(Route::getCurrentRoute()->uri(), 'home') !== false || strpos(Route::getCurrentRoute()->uri(), 'banner') !== false )
            <ul class="nav sub-nav" style="display:block">
            @else
            <ul class="nav sub-nav">
            @endif

              @if( strpos(Route::getCurrentRoute()->uri(), 'home') !== false || strpos(Route::getCurrentRoute()->uri(), 'banner') !== false )
              <li class="dropNav myDropNav active">
              @else
              <li class="dropNav myDropNav">
              @endif
                  <a class="firstDrop" href="admin/company/category">
                  <span class="fa fa-columns"></span>
                  <span>公司管理</span>
                </a>
              </li>

            </ul>      
          </li>

        <!-- 期权管理 -->
        @if( strpos(Route::getCurrentRoute()->uri(), 'stock') !== false )
            <li class="dropNav myDropNav active">
              <a class="firstDrop accordion-toggle menu-open">
          @else
            <li class="dropNav myDropNav">
              <a class="firstDrop accordion-toggle">
          @endif
              <span class="glyphicon glyphicon-question-sign"></span>
              <span class="sidebar-title">期权管理</span>
              <span class="caret"></span>
            </a>

            @if( strpos(Route::getCurrentRoute()->uri(), 'stock') !== false )
            <ul class="nav sub-nav" style="display:block">
            @else
            <ul class="nav sub-nav">
            @endif

              @if( strpos(Route::getCurrentRoute()->uri(),'list') !== false )
              <li class="dropNav myDropNav active">
              @else
              <li class="dropNav myDropNav">
              @endif
                  <a class="firstDrop" href="admin/stock/list">
                  <span class="fa fa-columns"></span>
                  <span>期权管理</span>
                </a>
              </li>

              @if( strpos(Route::getCurrentRoute()->uri(), 'exerciselist') !== false )
              <li class="dropNav myDropNav active">
              @else
              <li class="dropNav myDropNav">
              @endif
                  <a class="firstDrop" href="admin/stock/exerciselist">
                  <span class="fa fa-columns"></span>
                  <span>行权管理</span>
                </a>
              </li>

            </ul>      
          </li>

        <!-- 用户管理 -->
        @if( strpos(Route::getCurrentRoute()->uri(), 'user') !== false )
          <li class="dropNav myDropNav active">
            <a class="firstDrop accordion-toggle menu-open">
        @else
          <li class="dropNav myDropNav">
            <a class="firstDrop accordion-toggle">
        @endif
              <span class="glyphicon glyphicon-user"></span>
              <span class="sidebar-title">用户管理</span>
              <span class="caret"></span>
            </a>

            @if( strpos(Route::getCurrentRoute()->uri(), 'user') !== false )
            <ul class="nav sub-nav" style="display:block">
            @else
            <ul class="nav sub-nav">
            @endif

              @if( strpos(Route::getCurrentRoute()->uri(), 'usermsg') !== false )
              <li class="dropNav myDropNav active">
              @else
              <li class="dropNav myDropNav">
              @endif
                  <a class="firstDrop" href="admin/usermsg">
                  <span class="fa fa-columns"></span>
                  <span>用户信息</span>
                </a>
              </li>

              @if( strpos(Route::getCurrentRoute()->uri(), 'user_verify') !== false )
              <li class="dropNav myDropNav active">
              @else
              <li class="dropNav myDropNav">
              @endif
                  <a class="firstDrop" href="admin/user_verify">
                  <span class="fa fa-columns"></span>
                  <span>认证资料</span>
                </a>
              </li>

              @if( strpos(Route::getCurrentRoute()->uri(), 'user_bindcard') !== false )
              <li class="dropNav myDropNav active">
              @else
              <li class="dropNav myDropNav">
              @endif
                  <a class="firstDrop" href="admin/user_bindcard">
                  <span class="fa fa-columns"></span>
                  <span>银行卡绑定</span>
                </a>
              </li>

            </ul>      
          </li>

         <!-- 人民币管理 -->
          @if( strpos(Route::getCurrentRoute()->uri(), 'yen') !== false )
            <li class="dropNav myDropNav active">
              <a class="firstDrop accordion-toggle menu-open">
          @else
            <li class="dropNav myDropNav">
              <a class="firstDrop accordion-toggle">
          @endif
              <span class="glyphicon glyphicon-yen"></span>
              <span class="sidebar-title">人民币</span>
              <span class="caret"></span>
            </a>

            @if( strpos(Route::getCurrentRoute()->uri(), 'yen') !== false )
            <ul class="nav sub-nav" style="display:block">
            @else
            <ul class="nav sub-nav">
            @endif

              @if( strpos(Route::getCurrentRoute()->uri(),'admin/yen/recharge') !== false )
              <li class="dropNav myDropNav active">
              @else
              <li class="dropNav myDropNav">
              @endif
                  <a class="firstDrop" href="admin/yen/recharge">
                  <span class="fa fa-columns"></span>
                  <span>充值列表</span>
                </a>
              </li>

              @if( strpos(Route::getCurrentRoute()->uri(),'admin/yen/withdraw') !== false )
              <li class="dropNav myDropNav active">
              @else
              <li class="dropNav myDropNav">
              @endif
                  <a class="firstDrop" href="admin/yen/withdraw">
                  <span class="fa fa-columns"></span>
                  <span>提现管理</span>
                </a>
              </li>

            </ul>      
          </li>

         
          <!-- 数据对账 -->
          @if( strpos(Route::getCurrentRoute()->uri(), 'statistics') !== false )
            <li class="dropNav myDropNav active">
              <a class="firstDrop accordion-toggle menu-open">
          @else
            <li class="dropNav myDropNav">
              <a class="firstDrop accordion-toggle">
          @endif
              <span class="glyphicon glyphicon-equalizer"></span>
              <span class="sidebar-title">数据统计对账</span>
              <span class="caret"></span>
            </a>

            @if( strpos(Route::getCurrentRoute()->uri(), 'statistics') !== false )
            <ul class="nav sub-nav" style="display:block">
            @else
            <ul class="nav sub-nav">
            @endif

              @if( strpos(Route::getCurrentRoute()->uri(),'admin/statistics/yuanstat') !== false )
              <li class="dropNav myDropNav active">
              @else
              <li class="dropNav myDropNav">
              @endif
                  <a class="firstDrop" href="admin/statistics/yuanstat">
                  <span class="fa fa-columns"></span>
                  <span>人民币统计</span>
                </a>
              </li>
              @if( strpos(Route::getCurrentRoute()->uri(),'admin/statistics/yuancash') !== false )
              <li class="dropNav myDropNav active">
              @else
              <li class="dropNav myDropNav">
              @endif
                  <a class="firstDrop" href="admin/statistics/yuancash">
                  <span class="fa fa-columns"></span>
                  <span>用户账户人民币</span>
                </a>
              </li>
            </ul>      
          </li>




           <!-- 股权管理 -->
          @if( strpos(Route::getCurrentRoute()->uri(), 'digitalcoin') !== false )
            <li class="dropNav myDropNav active">
              <a class="firstDrop accordion-toggle menu-open">
          @else
            <li class="dropNav myDropNav">
              <a class="firstDrop accordion-toggle">
          @endif
              <span class="glyphicon glyphicon-bitcoin"></span>
              <span class="sidebar-title">股权管理</span>
              <span class="caret"></span>
            </a>

            @if( strpos(Route::getCurrentRoute()->uri(), 'digitalcoin') !== false )
            <ul class="nav sub-nav" style="display:block">
            @else
            <ul class="nav sub-nav">
            @endif

              @if( strpos(Route::getCurrentRoute()->uri(),'admin_roles') !== false )
              <li class="dropNav myDropNav active">
              @else
              <li class="dropNav myDropNav">
              @endif
                  <a class="firstDrop" href="admin/stockmanagement/stock">
                  <span class="fa fa-columns"></span>
                  <span>股权列表</span>
                </a>
              </li>
            </ul>      
          </li>


          <!-- 用户权限管理 -->
          @if( strpos(Route::getCurrentRoute()->uri(), 'admin1') !== false )
            <li class="dropNav myDropNav active">
              <a class="firstDrop accordion-toggle menu-open">
          @else
            <li class="dropNav myDropNav">
              <a class="firstDrop accordion-toggle">
          @endif
              <span class="glyphicon glyphicon-lock"></span>
              <span class="sidebar-title">账户权限管理</span>
              <span class="caret"></span>
            </a>

            @if( strpos(Route::getCurrentRoute()->uri(), 'admin1') !== false )
            <ul class="nav sub-nav" style="display:block">
            @else
            <ul class="nav sub-nav">
            @endif

              @if( strpos(Route::getCurrentRoute()->uri(),'admin_roles') !== false )
              <li class="dropNav myDropNav active">
              @else
              <li class="dropNav myDropNav">
              @endif
                  <a class="firstDrop" href="admin/admin_roles">
                  <span class="fa fa-columns"></span>
                  <span>角色管理</span>
                </a>
              </li>

              @if( strpos(Route::getCurrentRoute()->uri(), 'admin_account') !== false )
              <li class="dropNav myDropNav active">
              @else
              <li class="dropNav myDropNav">
              @endif
                  <a class="firstDrop" href="admin/admin_account">
                  <span class="fa fa-columns"></span>
                  <span>账户管理</span>
                </a>
              </li>

            </ul>      
          </li>


      </ul>

      
      <!-- End: Sidebar Menu -->

      <!-- Start: Sidebar Collapse Button -->
      <div class="sidebar-toggle-mini">
        <a href="#">
          <span class="fa fa-sign-out"></span>
        </a>
      </div>
      <!-- End: Sidebar Collapse Button -->

    </div>
    <!-- End: Sidebar Left Content -->

  </aside>
   