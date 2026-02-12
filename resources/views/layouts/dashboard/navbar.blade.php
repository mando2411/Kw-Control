<?php if(auth()->user()){ ?>
    <div class="nav d-flex justify-content-between align-items-center px-2 bg-dark fixed-top w-100 flex-wrap">
        <div class="f-nav d-flex pt-2 align-items-center " >
            <a href="">
                <figure class="rounded-circle overflow-hidden me-2"><img src="{{auth()->user()->image ? auth()->user()->image : asset("assets/admin/images/users/user-placeholder.png")  }}" class="w-100 h-100" alt="user image"></figure>
            </a>
            <figure class="position-relative me-2">
                <img src="{{asset("assets/admin/images/image3.png")}}" class="w-100 h-100" alt="currennt date image">
                <p class="todayDate fs-4 position-absolute fw-bold">3</p>
            </figure>
            <p class="text-secondary">
                <strong>Control</strong>
                <br>
                <strong>9559 8151</strong>
            </p>
        </div>
    
        <div class=" py-2 text-end ">
            <div class="d-flex navControll">
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
                <span class="media-body text-end switch-sm mx-2">
                    <button type="button" id="sidebar-toggle" class="btn btn-outline-secondary" aria-label="Toggle sidebar">
                        <i data-feather="align-left"></i>
                    </button>
                </span>
                @endif
            </div>
    
        </div>
    
    </div>
<?php } ?>