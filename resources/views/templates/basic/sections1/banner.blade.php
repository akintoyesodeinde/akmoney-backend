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
                                                                <div class="title" id="senderCode">@{{ $sendFrom.code }}</div>
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
                                                                {{ trans('Amount must be at least') }} @{{ parseFloat(minAmount*senderCurrencyRate).toFixed(2) }} @{{ $sendFrom.code }}
                                                            </div>
                                                            <div class="fw-500 text-danger" v-cloak
                                                                v-else-if="send_amount / senderCurrencyRate > maxAmount">
                                                                {{ trans('Amount must not exceed') }} @{{ maxAmount*senderCurrencyRate }} @{{ $sendFrom.code }}
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
                                            {{ trans('Transfer fee:') }} <strong v-cloak>@{{ $transferFee.toFixed(2) }} USD</strong>,
                                            <span style="font-size:14px;">Exchange Rate: 1 @{{$sendFrom.code}} = <strong style="font-size:14px;" v-cloak>@{{ parseFloat(senderCurrencyRate).toFixed(2) }} USD</strong></span> 
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
                                                                <div class="title" id="receiverCode">@{{ $receiveFrom.code }}</div>
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
                                                            <div id="receiverCurrName">@{{ $receiveFrom.name }}</div>
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
                                    <div class="title" id="senderCode">@{{ $item.code }}</div>
                                    <div class="sub-title">@{{ $item.name }}</div>
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
                                    <div class="title">@{{ $item.code }}</div>
                                    <div class="sub-title">@{{ $item.name }}</div>
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