@php
    $counterElements = getContent('counter.element', false, null, true);
@endphp
<!--<div class="counter-section">-->
<!--    <div class="container">-->
<!--        <div class="row g-4 justify-content-center">-->

<!--            @foreach (@$counterElements ?? [] as $item)-->
<!--                <div class="col-6 col-md-4 col-xl-3">-->
<!--                    <div class="counter-box counter-up rounded-4 text-center h-100">-->

<!--                        <div class="counter-icon mb-3">-->
<!--                            {!! $item->data_values->icon !!}-->
<!--                        </div>-->
                        
<!--                        <h2 class="counter-value mb-0">-->
<!--                            <span class="counter-up__counter">-->
<!--                            <span class="odometer" data-odometer-final="{{ $item->data_values->digit }}">0</span>-->
<!--                            </span>-->
<!--                            <span class="plus">+</span>-->
<!--                        </h2>-->

<!--                        <p class="counter-title mt-2 mb-0">-->
<!--                            {{ __($item->data_values->title) }}-->
<!--                        </p>-->

<!--                    </div>-->
<!--                </div>-->
<!--            @endforeach-->

<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<section class="rt-main-feature rt-section-pt rt-section-pb overflow-hidden">
    <div class="container custom-container">
        <div class="row justify-content-center">
            <div class="col-xxl-6 col-xl-8 col-lg-10 aos-init" data-aos="fade-up">
                <div class="rt-section-header text-center">
                    <span class="rt-section-caption">Why Choose {!! siteNameHtml() !!}</span>
                    <h2 class="rt-section-title">Fast, compliant cross-border transfers</h2>
                    <p class="mb-0">Send money from Canada to Africa & Middle East with just a few taps. Experience quick, secure transfers with competitive exchange rates and transparent fees.</p>
                </div>
            </div>
        </div>

        <div class="rt-main-feature-wrapper">
            <div class="row gy-4 justify-content-center overflow-hidden">
                <div class="col-md-6 aos-init" data-aos="fade-up">
                    <div class="rt-main-feature-item">
                        <div class="icon">
                            <img src="{{asset('new_assetts/ticker1.png')}}" alt="Fast transfers icon">
                        </div>
                        <div class="content">
                            <h3 class="h4">Instant Transfers</h3>
                            <p class="mb-0">Send money to Africa & Middle East in minutes with our fast transfer network and local partnerships.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 aos-init" data-aos="fade-up">
                    <div class="rt-main-feature-item">
                        <div class="icon">
                            <img src="{{asset('new_assetts/ticker2.png')}}" alt="Transparent fees icon">
                        </div>
                        <div class="content">
                            <h3 class="h4">Transparent Fees</h3>
                            <p class="mb-0">Enjoy clear pricing with competitive fees for cross-border transfers from Canada.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 aos-init" data-aos="fade-up">
                    <div class="rt-main-feature-item">
                        <div class="icon">
                            <img src="{{asset('new_assetts/ticker3.png')}}" alt="Secure transfers icon">
                        </div>
                        <div class="content">
                            <h3 class="h4">Secure & Compliant</h3>
                            <p class="mb-0">Canadian MSB-regulated service with bank-level security and AML compliance.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 aos-init" data-aos="fade-up">
                    <div class="rt-main-feature-item">
                        <div class="icon">
                            <img src="{{asset('new_assetts/ticker4.png')}}" alt="Multiple options icon">
                        </div>
                        <div class="content">
                            <h3 class="h4">Flexible Delivery</h3>
                            <p class="mb-0">Recipients can receive funds via bank deposit, cash pickup or mobile wallet across our corridors.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <img src="{{asset('new_assetts/shape-bg.png')}}" alt="{{ gs('site_name') }} background shape" class="rt-main-feature-shape">
</section>

