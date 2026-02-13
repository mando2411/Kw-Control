<?php if(auth()->user()){ ?>
    <!-- Desktop header (kept as-is, modern gets a separate mobile header) -->
    <div class="nav dashboard-topbar dashboard-topbar-desktop d-flex justify-content-between align-items-center px-2 bg-dark fixed-top w-100 flex-wrap">
        <div class="f-nav d-flex pt-2 align-items-center " >
            <a href="">
                <figure class="rounded-circle overflow-hidden me-2"><img src="{{ auth()->user()->image ? auth()->user()->image : asset('assets/admin/images/users/user-placeholder.png') }}" onerror="this.onerror=null;this.src='{{ asset('assets/admin/images/users/user-placeholder.png') }}';" class="w-100 h-100" alt="user image"></figure>
            </a>
            <figure class="position-relative me-2">
                <img src="{{asset("assets/admin/images/image3.png")}}" class="w-100 h-100" alt="currennt date image">
                <p class="todayDate fs-4 position-absolute fw-bold">3</p>
            </figure>
            <p class="text-secondary">
                <strong>{{ config('app.name', 'كنترول') }}</strong>
                <br>
                <strong>9559 8151</strong>
            </p>
        </div>
    
        <div class=" py-2 text-end ">
            <div class="d-flex navControll">
                <div class="dropdown me-1" id="notif-menu-wrapper">
                    <button type="button" id="notif-menu-dropdown" class="btn btn-outline-secondary notif-toggle" aria-label="الإشعارات" title="الإشعارات" onclick="toggleDashboardNotifMenu(event)" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        <span class="dtm-notif-badge" id="dtmNotifBadge" hidden>0</span>
                    </button>
                    <div id="notif-menu-panel" class="dropdown-menu dtm-notif-panel" dir="rtl">
                        <div class="dtm-notif-head">
                            <strong>الإشعارات</strong>
                            <button type="button" class="dtm-notif-read-all" id="dtmNotifReadAll">تعليم الكل كمقروء</button>
                        </div>
                        <div class="dtm-notif-list" id="dtmNotifList">
                            <div class="dtm-notif-empty">لا توجد إشعارات حالياً.</div>
                        </div>
                    </div>
                </div>

                                <div class="dropdown me-1" id="user-menu-wrapper">
                                    <button id="user-menu-dropdown" class="btn btn-secondary dropdown-toggle" type="button" onclick="toggleDashboardUserMenu(event)" aria-expanded="false">
                        <i class="fa fa-user ms-1"></i>
                        <span class="font-sm text-capitalize">                 {{auth()->user()->name}}
                        </span>
                    </button>
                                        <ul id="user-menu-panel" class="dropdown-menu ">
                      <li><a class="dropdown-item px-2" style="font-size:12px" href="{{ route('profile.edit') }}" >تحرير الملف الشخصي</a></li>
                      <li><a class="dropdown-item px-2 text-danger" style="font-size:12px" href="{{route('logout')}}"><i class="bi bi-box-arrow-left m-1 fs-6 "></i>تسجيل خروج</a></li>
                    </ul>
                  </div>
    
    
                <a href="{{route('dashboard')}}" class="home-tn">
                    <button class="btn btn-outline-secondary">
                    <i class="fa fa-home fs-5"></i>
    
                </button>
                </a>
                @if (auth()->user()->hasRole("Administrator"))
                <span class="media-body text-end switch-sm mx-2 sidebar-toggle-classic">
                    <label class="switch">
                        <a href="javascript:void(0)" class="btn btn-outline-secondary" aria-label="القائمة الجانبية">
                            <i id="sidebar-toggle" data-feather="align-left"></i>
                        </a>
                    </label>
                </span>

                <span class="media-body text-end mx-2 sidebar-toggle-modern">
                    <button type="button" class="hm-sidebar-toggle" id="sidebar-toggle-modern" aria-label="القائمة الجانبية">
                        <i class="bi bi-layout-sidebar-inset"></i>
                        <span class="hm-sidebar-toggle-text">القائمة</span>
                    </button>
                </span>
                @endif
            </div>
    
        </div>
    
    </div>

    <!-- Modern Mobile Header (ui-modern only via CSS) -->
    <div class="dashboard-topbar-mobile fixed-top" dir="rtl" aria-label="Mobile Header">
        <div class="dtm-inner">
            <div class="dtm-left">
                <div class="dropdown" id="user-menu-wrapper-mobile">
                    <button id="user-menu-dropdown-mobile" class="dtm-user" type="button" onclick="toggleDashboardUserMenuMobile(event)" aria-expanded="false" aria-label="حساب المستخدم">
                        <img class="dtm-avatar" src="{{ auth()->user()->image ? auth()->user()->image : asset('assets/admin/images/users/user-placeholder.png') }}" onerror="this.onerror=null;this.src='{{ asset('assets/admin/images/users/user-placeholder.png') }}';" alt="user image">
                    </button>
                    <ul id="user-menu-panel-mobile" class="dropdown-menu">
                        <li><a class="dropdown-item px-2" style="font-size:12px" href="{{ route('profile.edit') }}">تحرير الملف الشخصي</a></li>
                        <li><a class="dropdown-item px-2 text-danger" style="font-size:12px" href="{{route('logout')}}"><i class="bi bi-box-arrow-left m-1 fs-6 "></i>تسجيل خروج</a></li>
                    </ul>
                </div>

                <a href="{{ route('dashboard') }}" class="dtm-brand" aria-label="الرئيسية">
                    <span class="dtm-title">{{ config('app.name', 'كنترول') }}</span>
                </a>
            </div>

            <div class="dtm-center" aria-hidden="true"></div>

            <div class="dtm-actions">
                <a href="{{ route('logout') }}" class="dtm-logout" aria-label="تسجيل خروج">
                    <i class="bi bi-box-arrow-right"></i>
                </a>

                <div class="dropdown" id="notif-menu-wrapper-mobile">
                    <button type="button" id="notif-menu-dropdown-mobile" class="dtm-notif" aria-label="الإشعارات" title="الإشعارات" onclick="toggleDashboardNotifMenuMobile(event)" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        <span class="dtm-notif-badge" id="dtmNotifBadgeMobile" hidden>0</span>
                    </button>
                    <div id="notif-menu-panel-mobile" class="dropdown-menu dtm-notif-panel" dir="rtl">
                        <div class="dtm-notif-head">
                            <strong>الإشعارات</strong>
                            <button type="button" class="dtm-notif-read-all" id="dtmNotifReadAllMobile">تعليم الكل كمقروء</button>
                        </div>
                        <div class="dtm-notif-list" id="dtmNotifListMobile">
                            <div class="dtm-notif-empty">لا توجد إشعارات حالياً.</div>
                        </div>
                    </div>
                </div>

                @if (auth()->user()->hasRole("Administrator"))
                    <button type="button" class="hm-sidebar-toggle hm-sidebar-toggle--mobile" id="sidebar-toggle-modern-mobile" aria-label="القائمة الجانبية">
                        <i class="bi bi-layout-sidebar-inset"></i>
                    </button>
                @endif
            </div>

        </div>
    </div>

    <!-- Modern Mobile Bottom Bar (ui-modern only via CSS) -->
    <nav class="dashboard-mobilebar" dir="rtl" aria-label="Mobile Bar">
        <a href="{{ route('dashboard') }}" class="dmb-item" aria-label="الرئيسية">
            <i class="bi bi-house"></i>
            <span>الرئيسية</span>
        </a>
        @if (auth()->user()->hasRole("Administrator"))
            <a href="{{ route('dashboard.settings.result') }}" class="dmb-item" aria-label="الإعدادات">
                <i class="bi bi-sliders"></i>
                <span>الإعدادات</span>
            </a>
        @endif
        <a href="{{ route('profile.edit') }}" class="dmb-item" aria-label="الملف الشخصي">
            <i class="bi bi-person"></i>
            <span>حسابي</span>
        </a>
    </nav>
<?php } ?>