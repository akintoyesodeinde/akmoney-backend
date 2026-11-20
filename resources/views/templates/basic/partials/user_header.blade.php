@php
$user = auth()->user();
@endphp

<header class="header-section body-collapse">
    <div class="overlay">
        <div class="container-fluid">
            <div class="row d-flex header-area">
                <div class="navbar-area d-flex align-items-center justify-content-between">
                    
                    <div class="sidebar-icon sidebar-toggler">
                        <img src="{{ asset('assets/images/menu.png') }}" alt="icon">
                    </div>

                    <div class="col-xl-6 d-xl-block">
                        <ul class="rt-header-menu justify-content-center" style="color:black;">
                            <li>
                                <a class="col-xl-6 d-xl-block d-none" style="color: #084b5f" href="{{url('/')}}">Home</a>
                            </li>

                            <li>
                                <a style="color: #084b5f" href="{{url('/about')}}">About</a>
                            </li>
                            <li>
                                <a style="color: #084b5f" href="{{url('/blog')}}">Blog</a>
                            </li>
                            <!--<li>-->
                            <!--    <a style="color: #084b5f" href="{{url('/faq')}}">Faq</a>-->
                            <!--</li>-->
                            <li>
                                <a style="color: #084b5f" href="{{url('/contact')}}">Contact</a>
                            </li>
                        </ul>
                    </div>

                    <div class="dashboard-nav">
                        <div class="single-item user-area">
                            <div class="profile-area d-flex align-items-center">
                                <span class="user-profile">
                                    <img src="{{ asset($user->image ? 'assets/images/user/profile/'.$user->image : 'assets/images/avatar.png') }}"
                                        alt="User" class="rounded-circle" style="width:60px;height:60px;object-fit:cover;">
                                </span>
                                <i class="fa-solid fa-sort-down"></i>
                            </div>
                            <div class="main-area user-content">
                                <div class="head-area d-flex align-items-center">
                                    <div class="profile-head">
                                        <a href="{{ route('user.profile.setting') }}">
                                            <h5>{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</h5>
                                        </a>
                                    </div>
                                </div>
                                <ul>
                                    <li><a href="{{ route('user.profile.setting') }}"><i class="fas fa-cog"></i> <span style="padding-left:5px;">Settings</span></a></li>
                                    <li><a href="{{ route('user.twofactor') }}"><i class="fa-solid fa-shield-halved"></i> <span style="padding-left:5px;">2FA Security</span></a></li>
                                    <li><a href="{{ route('user.change.password') }}"><i class="fas fa-key"></i> <span style="padding-left:5px;">Change Password</span></a></li>
                                    <li><a href="{{ route('user.logout') }}"><i class="fas fa-sign-out"></i> <span style="padding-left:5px;">Logout</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!--<header class="rt-header">-->
<!--        <div class="container custom-container">-->
<!--            <div class="rt-header-inner">-->
<!--                <div class="row align-items-center justify-content-between">-->
<!--                    <div class="col-xl-6 d-xl-block d-none">-->
<!--                        <ul class="rt-header-menu justify-content-center">-->
<!--                            <li>-->
<!--                                <a href="{{url('/')}}">Home</a>-->
<!--                            </li>-->

<!--                            <li>-->
<!--                                <a href="{{url('/about')}}">About</a>-->
<!--                            </li>-->
<!--                            <li>-->
<!--                                <a href="{{url('/blog')}}">Blog</a>-->
<!--                            </li>-->
                            <!--<li>-->
                                <!--<a href="{{url('/faq')}}">Faq</a>-->
                            <!--</li>-->
<!--                            <li>-->
<!--                                <a href="{{url('/contact')}}">Contact</a>-->
<!--                            </li>-->
<!--                        </ul>-->
<!--                    </div>-->
<!--                    <div class="col-xl-4 col-5 d-flex align-items-center justify-content-end gap-3">-->
<!--                        <div class="rt-header-btns">-->
<!--                            <div class="dropdown dropdown-center">-->

                                
<!--                            </div>-->

<!--                            <div class="d-xl-flex d-none align-items-center gap-3">-->
<!--                                @guest-->
<!--                                <a href="{{route('user.login')}}"-->
<!--                                    class="btn btn-md rt-solid-btn-primary">Login</a>-->
<!--                                <a href="{{route('user.register')}}"-->
<!--                                    class="btn btn-md rt-solid-btn-primary">Register</a>-->
<!--                                @else-->
<!--                                <a href="{{ route('user.home') }}"-->
<!--                                    class="btn btn-md rt-solid-btn-primary">Dashboard</a>-->
<!--                                @endguest    -->
<!--                            </div>-->

<!--                        </div>-->
<!--                        <div class="text-end">-->
<!--                            <button type="button" class="mobile-menu-open">-->
<!--                                <i class="ri-bar-chart-horizontal-line"></i>-->
<!--                            </button>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </header>-->

    <!--<div class="mobile-menu">-->
    <!--    <button type="button" class="mobile-menu-close">-->
    <!--        <i class="ri-close-line"></i>-->
    <!--    </button>-->
    <!--    <ul class="rt-header-menu">-->
    <!--        <li>-->
    <!--            <a href="{{url('/')}}">Home</a>-->
    <!--        </li>-->

    <!--        <li>-->
    <!--            <a href="{{url('/about')}}">About</a>-->
    <!--        </li>-->
    <!--        <li>-->
    <!--            <a href="{{url('/blog')}}">Blog</a>-->
    <!--        </li>-->
    <!--        <li>-->
    <!--            <a href="{{url('/faq')}}">Faq</a>-->
    <!--        </li>-->
    <!--        <li>-->
    <!--            <a href="{{url('contact')}}">Contact</a>-->
    <!--        </li>-->

    <!--    </ul>-->

    <!--    <div class="mobile-menu-bottom">-->
    <!--        @guest-->
    <!--        <div class="mt-3">-->
    <!--            <a href="{{route('user.login')}}"-->
    <!--                class="btn rt-solid-btn-primary rounded-2 w-100">Login</a>-->
    <!--        </div>-->
    <!--        <div class="mt-3">-->
    <!--            <a href="{{route('user.register')}}"-->
    <!--                class="btn rt-solid-btn-primary rounded-2 w-100">Register</a>-->
    <!--        </div>-->
    <!--        @else-->
            
    <!--        <div class="mt-3">-->
    <!--            <a href="{{route('user.home')}}"-->
    <!--                class="btn rt-solid-btn-primary rounded-2 w-100">Dashboard</a>-->
    <!--        </div>-->
            
    <!--        @endguest -->
    <!--    </div>-->
    <!--</div>-->

@push('style')
@endpush