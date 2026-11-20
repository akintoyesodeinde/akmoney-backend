@php
    $bannerContent = getContent('banner.content', true);

    $data = session()->get('intended_info');
    session()->forget('intended_info');
    $sendingAmount = @$data['form_data']['sending_amount'];
    $recipientAmount = @$data['form_data']['recipient_amount'];
    $deliveryMethodId = @$data['form_data']['delivery_method'];
    $recipientCountryId = @$data['form_data']['recipient_country'];
    $sendingCountryId = @$data['form_data']['sending_country'];
    $counterElements = getContent('counter.element', false, null, true);
@endphp
<div id="MoneyTransfer">
    <section class="rt-banner">
        <div class="container custom-container">
            <div class="row gy-5 justify-content-xl-between justify-content-center align-items-center">
                <div class="col-xxl-6 col-xl-6 col-lg-6 text-lg-start text-center" data-aos="fade-right">
                    <h2 class="rt-banner-title">
                        Fast, Secure Money Transfers from Canada to Africa & Middle East
                    </h2>
                    <p class="rt-banner-description">
                        {!! siteNameHtml() !!} makes sending money across borders simple, with competitive exchange rates and transparent fees.
                        Enjoy real-time tracking and multiple delivery options to support loved ones and businesses internationally.
                    </p>
                    <div
                        class="d-flex flex-wrap align-items-center gap-sm-4 gap-3 mt-4 pt-2 justify-content-lg-start justify-content-center">
                        <a href="{{ route('user.register') }}"
                            class="btn btn-lg rt-solid-btn-primary">
                            Start Sending Now
                            <i class="ri-arrow-right-line"></i>
                        </a>
                        <a href="{{ url('/about') }}"
                            class="btn btn-lg rt-outline-btn-primary">
                            Learn More
                            <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-6" data-aos="fade-left">
                      <div id="MoneyTransfer" v-cloak>
                     <div class="calculator-section">
                        <div class="calculator" id="MoneyTransfer2">
                            <div class="calculator-header">
                                <h4>Send Money from Canada</h4>
                                <p>Canadian MSB-regulated transfers with competitive rates</p>
                            </div>
                            <form action="" method="post">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-amount-box" id="inputAmountBox">
                                            <span class="linear-gradient"></span>
                                            <div class="input-amount-wrapper">
                                                <label class="form-label mb-2">@lang('You send exactly')</label>
                                                <div class="input-amount-box-inner" id="inputAmountBoxInner">
                                                    <a href="#" class="icon-area" data-bs-toggle="modal"
                                                        data-bs-target="#senderModal">
                                                        <img class="img-flag" id="senderImage" :src="sendFrom.image" alt="">
                                                    </a>
                                                    <div class="text-area w-100">
                                                        <div class="d-flex gap-3 justify-content-between">
                                                            <a href="#" class="currency-name d-flex align-items-center gap-1"
                                                                data-bs-toggle="modal" data-bs-target="#senderModal">
                                                                <div class="title" id="senderCode">@{{ sendFrom.code }}</div>
                                                                <i class="fa-regular fa-angle-down"></i>
                                                            </a>
                                                            
                                                            <input type="text" name="send_amount" id="send"
                                                                placeholder="0.00"
                                                                autocomplete="off"
                                                                v-model="send_amount"
                                                                @change="getValue" @keypress="onlyNumber"
                                                                @input="updateSenderAmount">
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <div class="sub-title" id="senderCurrName">@{{ sendFrom.name }}</div>
                                                            <div class="fw-500 text-danger" v-cloak
                                                                v-if="send_amount / senderCurrencyRate < minAmount">
                                                                {{ trans('Amount must be at least') }} @{{ parseFloat(minAmount*senderCurrencyRate).toFixed(2) }} @{{ sendFrom.code }}
                                                            </div>
                                                            <div class="fw-500 text-danger" v-cloak
                                                                v-else-if="send_amount / senderCurrencyRate > maxAmount">
                                                                {{ trans('Amount must not exceed') }} @{{ maxAmount*senderCurrencyRate }} @{{ sendFrom.code }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-description">
                                        <span class="single-description">
                                            <i class="fa-regular fa-arrow-right-arrow-left"></i>
                                            {{ trans('Transfer fee:') }} <strong v-cloak>@{{ transferFee.toFixed(2) }} USD</strong>,
                                            <span style="font-size:14px;">Exchange Rate: 1 @{{sendFrom.code}} = <strong style="font-size:14px;" v-cloak>@{{ parseFloat(senderCurrencyRate).toFixed(2) }} USD</strong></span> 
                                        </span>
                                    </div>

                                    <div class="col-12">
                                        <div class="input-amount-box" id="inputAmountBox2">
                                            <span class="linear-gradient"></span>
                                            <div class="input-amount-wrapper">
                                                <label class="form-label mb-2">@lang('Receiver Amount (Without Fee)')</label>
                                                <div class="input-amount-box-inner" id="inputAmountBoxInner2">
                                                    <a href="#" class="icon-area" data-bs-toggle="modal"
                                                        data-bs-target="#receiverModal">
                                                        <img class="img-flag" id="receiverImage" :src="receiveFrom.image" alt="">
                                                    </a>
                                                    <div class="text-area w-100">
                                                        <div class="d-flex gap-3 justify-content-between">
                                                            <a href="#" class="currency-name d-flex align-items-center gap-1"
                                                                data-bs-toggle="modal" data-bs-target="#receiverModal">
                                                                <div class="title" id="receiverCode">@{{ receiveFrom.code }}</div>
                                                                <i class="fa-regular fa-angle-down"></i>
                                                            </a>
                                                            <input type="text"
                                                                name="" id="" placeholder="0.00"
                                                                value="0.00"
                                                                v-model="get_amount" @change="sendValue"
                                                                @keypress="onlyNumber"
                                                                onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')"
                                                                @input="updateRecipientAmount">
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <div id="receiverCurrName">@{{ receiveFrom.name }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="btn-area">
                                        <a href="#" type="button" @click="goNext" class="btn btn-lg rt-solid-btn-primary w-100" :disabled="send_amount / senderCurrencyRate < minAmount || send_amount / senderCurrencyRate > maxAmount">
                                           @lang('continue')
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>

        <div class="rt-banner-slider">
            <div class="rt-banner-slide">
                <div class="rt-banner-item">
                    <img src="{{asset('new_assetts/slide4.jpg')}}"
                        alt="Family receiving money transfer in Africa" class="rt-banner-bg">
                </div>
            </div>
            <div class="rt-banner-slide">
                <div class="rt-banner-item">
                    <img src="{{asset('new_assetts/slide6.jpg')}}"
                        alt="Business making cross-border payment to Middle East" class="rt-banner-bg">
                </div>
            </div>
        </div>
    </section>

    <!-- Sender Modal -->
    <div class="modal fade calculator-modal" id="senderModal" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header flex-column">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="modal-title" id="staticBackdropLabel">Select a currency</h3>
                        <button type="button" class="cmn-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa-light fa-xmark"></i>
                        </button>
                    </div>
                    <div class="search-box mt-10">
                        <input type="text" id="search-input" onkeyup="filterItems('search-input')" class="form-control"
                            placeholder="Search here...">
                        <button type="submit" class="search-btn"><i class="far fa-search"></i></button>
                    </div>
                </div>
                <div class="modal-body">
                    <div id="currency-list" class="currency-list">
                        <div class="item" id="sendItem" v-for="item in senderCurrencies" @click="changeSender(item.id)">
                            <div class="left-side">
                                <div class="img-area">
                                    <img class="img-flag" :src="item.image" alt="" id="senderImgSrc">
                                </div>
                                <div class="text-area">
                                    <div class="title" id="senderCode">@{{ item.code }}</div>
                                    <div class="sub-title">@{{ item.name }}</div>
                                </div>
                            </div>
                            <div class="right-side" id="checkCurrency">
                                <i v-if="sendFrom.id === item.id" class="fa-sharp fas fa-circle-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receiver Modal -->
    <div class="modal fade calculator-modal" id="receiverModal" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header flex-column">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="modal-title" id="staticBackdropLabel">Select a currency</h3>
                        <button type="button" class="cmn-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa-light fa-xmark"></i>
                        </button>
                    </div>
                    <div class="search-box mt-10">
                        <input type="text" id="search-input2" onkeyup="filterItems('search-input2')" class="form-control"
                            placeholder="Search here...">
                        <button type="submit" class="search-btn"><i class="far fa-search"></i></button>
                    </div>
                </div>
                <div class="modal-body">
                    <div id="currency-list" class="currency-list">
                        <div class="item" v-for="item in receiverCurrencies" @click="changeReceive(item.id)">
                            <div class="left-side">
                                <div class="img-area">
                                    <img class="img-flag" :src="item.image" alt="">
                                </div>
                                <div class="text-area">
                                    <div class="title">@{{ item.code }}</div>
                                    <div class="sub-title">@{{ item.name }}</div>
                                </div>
                            </div>
                            <div class="right-side" id="checkCurrency">
                                <i v-if="receiveFrom.id === item.id" class="fa-sharp fas fa-circle-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--<section class="banner-section index">-->
<!--    <div class="overlay">-->
<!--        <div class="shape-area">-->
<!--             <img src="/assets/images/banner-box.png" class="obj-1" alt="image">-->
<!--            <img src="/assets/images/banner-human.png" class="obj-2" alt="image">-->
<!--            <img src="/assets/images/banner-rocket.png" class="obj-3" alt="image">-->
<!--            <img src="/assets/images/banner-clock.png" class="obj-4" alt="image">-->
<!--        </div>-->
<!--        <div class="banner-content">-->
<!--            <div class="container wow fadeInUp">-->
<!--                <div class="content-shape">r-->
<!--                    <img src="/assets/images/banner-wallet.png" class="obj-1" alt="image">-->
<!--                </div>-->
<!--                <div class="row justify-content-between align-items-center">-->
<!--                    <div class="col-lg-5 col-md-6">-->
<!--                        <div class="main-content">-->
<!--                            <div class="top-area section-text">-->
<!--                                <h5 class="sub-title">Trusted by over 3M customers</h5>-->
<!--                                <h1 class="title">{{ __(@$bannerContent->data_values->heading) }}</h1>-->
<!--                                <p class="xlr">{{ __(@$bannerContent->data_values->description) }}</p>-->
<!--                            </div>-->
<!--                            <div class="bottom-area d-xxl-flex">-->
<!--                                <a href="{{ $bannerContent->data_values->button_link ?? '' }}" class="cmn-btn">{{ __(@$bannerContent->data_values->button_text) }}</a>-->
<!--                                <a class="cmn-btn active mfp-iframe popupvideo" href="{{ $bannerContent->data_values->youtube_link ?? '' }}"><i class="fas fa-play"></i></a>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="col-sm-10 col-lg-5 col-xxl-4">-->-->
<!--                        <div class="exchange-form__header">-->
<!--                            <span class="exchange-form__sub-title text--white"> @lang('Conversion Rate') </span>-->
<!--                            <h5 class="exchange-form__title m-0 text-white">-->
<!--                                {{ showAmount(1, currencyFormat: false) }} <span class="sending-currency"></span> =-->
<!--                                <span class="exchange-rate">1</span> <span class="recipient-currency"></span>-->
<!--                            </h5>-->
<!--                        </div>-->
<!--                        <div class="exchange-form">-->
<!--                            <form action="{{ route('currency.calculator') }}" id="calculatorForm"-->
<!--                                class="row g-0 disableSubmission" method="POST">-->
<!--                                @csrf-->
<!--                                <div class="col-12">-->
<!--                                    <div class="exchange-form__body">-->
<!--                                        <div class="row g-3">-->
<!--                                            @include($activeTemplate . 'partials.country_fields', [
                                               'class' => 'mb-3',
                                              'showLimit' => false,
                                           ])
                                            <!--<div class="col-12">-->
<!--                                                <div class="select-wrapper">-->
<!--                                                    <label class="text--accent sm-text d-block fw-md mb-2"-->
<!--                                                        for="deliveryMethod">@lang('Delivery Methods')</label>-->
<!--                                                    <div class="">-->
<!--                                                        <select class="form-select form--select select2-basic"-->
<!--                                                            data-minimum-results-for-search="-1" id="deliveryMethod"-->
<!--                                                            name="delivery_method" required>-->
<!--                                                            <option value="">@lang('Select One')</option>-->
<!--                                                        </select>-->
<!--                                                    </div>-->
<!--                                                </div>-->
                                            <!--</div>-->
<!--                                            <div class="col-12 charge-section">-->
<!--                                                <ul class="list list--column timeline-list">-->
<!--                                                    <li class="list__item timeline-list__item">-->
<!--                                                        <span class="timeline-list__dot"></span>-->
<!--                                                        <div class="timeline-list__content justify-content-between">-->
<!--                                                            <div class="timeline-list__left">-->
<!--                                                                <span class="d-block sm-text">@lang('CHARGE')-->
<!--                                                                    <span class="chargeInfo" data-bs-toggle="tooltip"-->
<!--                                                                        title="@lang('Select delivery method and input amount to view charge rate')">-->
<!--                                                                        <i class="fa fa-info-circle"></i>-->
<!--                                                                    </span>-->
<!--                                                                </span>-->
<!--                                                            </div>-->
<!--                                                            <div class="timeline-list__right">-->
<!--                                                                +-->
<!--                                                                <span class="charge-amount-text">0</span>-->
<!--                                                                <span class="sending-currency"></span>-->
<!--                                                            </div>-->
<!--                                                        </div>-->
<!--                                                    </li>-->
<!--                                                    <li class="list__item timeline-list__item">-->
<!--                                                        <span class="timeline-list__dot"></span>-->
<!--                                                        <div class="timeline-list__content justify-content-between">-->
<!--                                                            <div class="timeline-list__left">-->
<!--                                                                <span class="d-block heading-clr">-->
<!--                                                                    @lang('Payable Amount')-->
<!--                                                                </span>-->
<!--                                                            </div>-->
<!--                                                            <div class="timeline-list__right">-->
<!--                                                                <span class="d-block heading-clr">-->
<!--                                                                    <span class="final-amount-text">0</span>-->
<!--                                                                    <span class="sending-currency"></span>-->
<!--                                                                </span>-->
<!--                                                            </div>-->
<!--                                                        </div>-->
<!--                                                    </li>-->
<!--                                                </ul>-->
<!--                                            </div>-->
<!--                                            <div class="col-12">-->
<!--                                                <button class="btn btn--xl btn--base w-100 btn--xl" type="submit">-->
<!--                                                    @lang('Send Now')-->
<!--                                                </button>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </form>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--    <div class="counter-section">-->
<!--        <div class="container wow fadeInUp">-->
<!--            <div class="row cus-mar">-->
<!--                @foreach (@$counterElements ?? [] as $item)-->
<!--                <div class="col-xl-3 col-md-3 col-sm-6">-->
<!--                    <div class="single-area d-flex align-items-center justify-content-center">-->
<!--                        <div class="text-area text-center">-->
<!--                            <h2 class="counter-value">-->
<!--                                <span class="counter-up__counter">-->
<!--                                <span class="counter" data-odometer-final="{{ $item->data_values->digit }}">{{ $item->data_values->digit }}</span><span>+</span>-->
<!--                                </span>-->
<!--                                </h2>-->
<!--                            <p>{{ __($item->data_values->title) }}</p>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--                @endforeach-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</section>-->


<!--<section class="hero"-->
<!--    style="background-image: url('{{ frontendImage('banner', @$bannerContent->data_values->image, '1800x840') }}')">-->
<!--<section class="hero banner-section index">-->
<!--    <div class="overlay">-->
<!--        <div class="shape-area">-->
<!--            <img src="{{ frontendImage('banner', @$bannerContent->data_values->image4) }}" class="obj-3" alt="image">-->
<!--        </div>-->
<!--        <div class="banner-content">-->
<!--            <div class="hero__content">-->
<!--                <div class="container">-->
<!--                    <div class="container wow-fallback">-->
<!--                        <div class="content-shape">-->
<!--                            <img src="{{ frontendImage('banner', @$bannerContent->data_values->image6) }}"-->
<!--                            class="content-obj-1" alt="wallet">-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="row gy-5 g-lg-4 align-items-center justify-content-center justify-content-lg-between">-->
<!--                        <div class="col-lg-7 col-xxl-6 text-lg-start text-center">-->
<!--                            <h2 class="hero__content-title text-capitalize text--white pt-lg-0 pt-4">-->
<!--                                {{ __(@$bannerContent->data_values->heading) }}-->
<!--                            </h2>-->
<!--                            <p class="hero__content-para text--white ms-lg-0 mx-auto">-->
<!--                                {{ __(@$bannerContent->data_values->description) }}-->
<!--                            </p>-->
<!--                            <div class="hero__btn-group justify-content-center justify-content-lg-start mt-4">-->
<!--                                <a class="btn btn--xl btn--base" href="{{ $bannerContent->data_values->button_link ?? '' }}">-->
<!--                                    {{ __(@$bannerContent->data_values->button_text) }}-->
<!--                                </a>-->
<!--                                <a class="btn btn--video btn--circle btn--base show-video"-->
<!--                                    href="{{ $bannerContent->data_values->youtube_link ?? '' }}">-->
<!--                                    <i class="fas fa-play"></i>-->
<!--                                </a>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="col-sm-10 col-lg-5 col-xxl-4">-->
<!--                            <div class="exchange-form__header">-->
<!--                                <span class="exchange-form__sub-title text--white"> @lang('Conversion Rate') </span>-->
<!--                                <h5 class="exchange-form__title m-0 text-white">-->
<!--                                    {{ showAmount(1, currencyFormat: false) }} <span class="sending-currency"></span> =-->
<!--                                    <span class="exchange-rate">1</span> <span class="recipient-currency"></span>-->
<!--                                </h5>-->
<!--                            </div>-->
<!--                            <div class="exchange-form">-->
<!--                                <form action="{{ route('currency.calculator') }}" id="calculatorForm"-->
<!--                                    class="row g-0 disableSubmission" method="POST">-->
<!--                                    @csrf-->
<!--                                    <div class="col-12">-->
<!--                                        <div class="exchange-form__body">-->
<!--                                            <div class="row g-3">-->
<!--                                                @include($activeTemplate . 'partials.country_fields', [
                                                   'class' => 'col-12',
                                                  'showLimit' => false,
                                               ])
<!--                                                <div class="col-12">-->
<!--                                                    <div class="select-wrapper">-->
<!--                                                        <label class="text--accent sm-text d-block fw-md mb-2"-->
<!--                                                            for="deliveryMethod">@lang('Delivery Methods')</label>-->
<!--                                                        <div class="form--select-light">-->
<!--                                                            <select class="form-select form--select select2-basic"-->
<!--                                                                data-minimum-results-for-search="-1" id="deliveryMethod"-->
<!--                                                                name="delivery_method" required>-->
<!--                                                                <option value="">@lang('Select One')</option>-->
<!--                                                            </select>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                                <div class="col-12 charge-section">-->
<!--                                                    <ul class="list list--column timeline-list">-->
<!--                                                        <li class="list__item timeline-list__item">-->
<!--                                                            <span class="timeline-list__dot"></span>-->
<!--                                                            <div class="timeline-list__content justify-content-between">-->
<!--                                                                <div class="timeline-list__left">-->
<!--                                                                    <span class="d-block sm-text">@lang('CHARGE')-->
<!--                                                                        <span class="chargeInfo" data-bs-toggle="tooltip"-->
<!--                                                                            title="@lang('Select delivery method and input amount to view charge rate')">-->
<!--                                                                            <i class="fa fa-info-circle"></i>-->
<!--                                                                        </span>-->
<!--                                                                    </span>-->
<!--                                                                </div>-->
<!--                                                                <div class="timeline-list__right">-->
<!--                                                                    +-->
<!--                                                                    <span class="charge-amount-text">0</span>-->
<!--                                                                    <span class="sending-currency"></span>-->
<!--                                                                </div>-->
<!--                                                            </div>-->
<!--                                                        </li>-->
<!--                                                        <li class="list__item timeline-list__item">-->
<!--                                                            <span class="timeline-list__dot"></span>-->
<!--                                                            <div class="timeline-list__content justify-content-between">-->
<!--                                                                <div class="timeline-list__left">-->
<!--                                                                    <span class="d-block heading-clr">-->
<!--                                                                        @lang('Payable Amount')-->
<!--                                                                    </span>-->
<!--                                                                </div>-->
<!--                                                                <div class="timeline-list__right">-->
<!--                                                                    <span class="d-block heading-clr">-->
<!--                                                                        <span class="final-amount-text">0</span>-->
<!--                                                                        <span class="sending-currency"></span>-->
<!--                                                                    </span>-->
<!--                                                                </div>-->
<!--                                                            </div>-->
<!--                                                        </li>-->
<!--                                                    </ul>-->
<!--                                                </div>-->
<!--                                                <div class="col-12">-->
<!--                                                    <button class="btn btn--xl btn--base w-100 btn--xl" type="submit">-->
<!--                                                        @lang('Send Now')-->
<!--                                                    </button>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </form>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</section>-->

@push('script')
    <script>
        "use strict";
        let agentStatus = `{{ gs('agent_module') }}`;
        let agent = `@lang('Agent')`;
        let agentFixedCharge = `{{ gs('agent_charges')->fixed_charge ?? 0 }}`;
        let agentPercentCharge = `{{ gs('agent_charges')->percent_charge ?? 0 }}`;

        let sendLimit = `{{ gs('send_money_limit') }}`;
        let sendingAmount = `{{ $sendingAmount }}`;
        let recipientAmount = `{{ $recipientAmount }}`;
        let deliveryMethodId = `{{ $deliveryMethodId }}`;
        let recipientCountryId = `{{ $recipientCountryId }}`;
        let sendingCountryId = `{{ $sendingCountryId }}`;

        if (deliveryMethodId) {
            deliveryMethodId *= 1;
        }

        let defaultSelectOption = `@lang('Select One')`;
        let serviceStatus = false;
    </script>

    <script src="{{ asset('assets/global/js/sendMoney.js') }}"></script>
@endpush

@push('style')
@endpush

