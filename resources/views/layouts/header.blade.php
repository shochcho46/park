<nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
    <div class="container-fluid"> <!--begin::Start Navbar Links-->
        <ul class="navbar-nav">
            <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i class="bi bi-list"></i> </a> </li>

        </ul> <!--end::Start Navbar Links--> <!--begin::End Navbar Links-->
        <ul class="navbar-nav ms-auto"> <!--begin::Navbar Search-->


            <li class="nav-item"> <a class="nav-link" href="#" data-lte-toggle="fullscreen"> <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i> <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i> </a> </li> <!--end::Fullscreen Toggle--> <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu"> <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"> <img src="{{asset('assets/img/usr.gif')}}" class="user-image rounded-circle shadow" alt="User Image"> <span class="d-none d-md-inline">

                @auth('web')
                    {{Auth::guard('web')->user()->name}}
                @endauth
                @auth('admin')
                    {{Auth::guard('admin')->user()->name}}
                @endauth
            </span> </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end"> <!--begin::User Image-->
                    <li class="user-header text-bg-primary"> <img src="{{asset('assets/img/usr.gif')}}" class="rounded-circle shadow" alt="User Image">
                        <p>
                            @auth('web')
                                {{Auth::guard('web')->user()->name}}
                            @endauth
                            @auth('admin')
                                {{Auth::guard('admin')->user()->name}}
                            @endauth
                        </p>
                    </li> <!--end::User Image--> <!--begin::Menu Body-->

                     <!--end::Menu Body--> <!--begin::Menu Footer-->
                    @auth('web')
                        <li class="user-footer"> <a href="#" class="btn btn-default btn-flat">Profile</a> <a href="{{route('user.logout')}}" class="btn btn-default btn-flat float-end">Sign out</a> </li> <!--end::Menu Footer-->

                    @endauth

                    @auth('admin')
                        <li class="user-footer"> <a href="#" class="btn btn-default btn-flat">Profile</a> <a href="{{route('admin.logout')}}" class="btn btn-default btn-flat float-end"> Sign Out</a> </li> <!--end::Menu Footer-->

                    @endauth


                </ul>
            </li> <!--end::User Menu Dropdown-->
        </ul> <!--end::End Navbar Links-->
    </div> <!--end::Container-->
</nav> <!--end::Header-->
