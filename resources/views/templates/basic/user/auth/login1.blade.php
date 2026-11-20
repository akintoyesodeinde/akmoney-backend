@extends($activeTemplate . 'layouts.frontend')
@php
    $loginContent = getContent('login.content', true);
@endphp
@section('content')
    <section class="login-reg">
        <div class="overlay pt-120">
            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-xl-6 order-xl-0 order-1">
                        <div class="sec-img d-rtl">
                            <img src="{{ getImage($activeTemplateTrue . 'images/loginbg.png') }}" class="max-un" alt="image">
                        </div>
                    </div>
                    <div class="col-xl-5">
                        <div class="section-text text-center">
                            <h5 class="sub-title">Account</h5>
                            <h2 class="title">Log in to Continue</h2>
                            <p class="dont-acc">Don’t have an account? <a href="{{ route('user.register') }}">Sign up</a></p>
                            @if(App\Models\Extension::where('act', 'google-auth')->where('status', 1)->exists())
                            <div class="reg-google">
                                <a href="{{route('user.redirectToGoogle')}}"><i class="fab fa-google"></i>Log in with Google</a>
                            </div>
                            <span class="or">Or Continue With</span>
                            @endif
                        </div>
                        <form action="{{ route('user.login') }}" id="loginForm" class="disableSubmission"
    autocomplete="off" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="single-input">
                                        <label for="logemail">Your Email</label>
                                        <input type="text" name="username" id="logemail" placeholder="Enter Your Email">
                                    </div>
                                    <div class="single-input">
                                        <label for="logpassword">Your Password</label>
                                        <input type="password" name="password" id="logpassword" placeholder="Enter Your Password">
                                    </div>
                                    <button class="cmn-btn w-100">Login</button>
                                </div>
                            </div>
                        </form>
                        <div class="forgot-pass text-center">
                            <a href="{{ route('user.password.request') }}">Forgot Password</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
