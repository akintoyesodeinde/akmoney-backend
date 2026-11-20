
@php
    $breadcrumbContent = getContent('breadcrumb.content', true);
@endphp
@if (!(request()->routeIs('pages') && request()->route('slug') === 'about'))
<section class="banner-section inner-banner privacy-content">
    <div class="overlay">
        @if (request()->routeIs('blog'))
        <div class="shape-area">
            <img src="{{ frontendImage('blog', @$breadcrumbContent->data_values->image, '1800x840') }}" class="shape-1" alt="image">
        </div>
        @endif
        <div class="banner-content d-flex align-items-center">
            <div class="container">
                <div class="row justify-content-start">
                    <div class="col-lg-7 col-md-10">
                        <div class="main-content">
                            <h1>{{ __($pageTitle) }}</h1>
                            <div class="breadcrumb-area">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb d-flex gap-1 align-items-center">
                                       <li class="breadcrumb-item p-0 position-relative"><a href="{{route('home')}}">Home</a></li>
                                       <li class="breadcrumb-item p-0 position-relative">
                                       <i class="la la-long-arrow-right"></i></li>
                                        <li class="breadcrumb-item p-0 position-relative active" aria-current="page">{{ __($pageTitle) }}</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@else
<section class="banner-section about-us">
    <div class="overlay">
        <div class="banner-content">
                <div class="container wow fadeInUp">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-lg-10">
                            <div class="main-content text-center">
                                <div class="top-area section-text dark-sec">
                                    <h5 class="sub-title">Built for entrepreneurs, by entrepreneurs</h5>
                                    <h1 class="title">The platform built to help businesses achieve their ambitions faster</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="banner-img-bottom pb-120">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="magnific-area d-flex align-items-center justify-content-around">
                            <div class="bg-area">
                                <img class="bg-item" src="../assets/images/about-popup.png" alt="image">
                            </div>
                            <a class="mfp-iframe popupvideo" href="https://www.youtube.com/watch?v=WOb4cj7izpE">
                                <img src="assets/images/popup-icon.png" alt="icon">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="stars-info pt-120">
                    <div class="row justify-content-center justify-content-around">
                        <div class="col-sm-3">
                            <div class="single-box">
                                <div class="icon-box">
                                    <img src="../assets/images/stats-info-icon-1.png" alt="icon">
                                </div>
                                <h5>2000+ employees & 100+ nationalities</h5>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="single-box">
                                <div class="icon-box">
                                    <img src="../assets/images/stats-info-icon-2.png" alt="icon">
                                </div>
                                <h5>27+ offices around the world</h5>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="single-box">
                                <div class="icon-box">
                                    <img src="../assets/images/stats-info-icon-3.png" alt="icon">
                                </div>
                                <h5>€303.6 BN billion in processed volume in 2020</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
