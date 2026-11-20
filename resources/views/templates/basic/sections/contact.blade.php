<section class="contact-section pt-0" style="margin-top:180px;">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="wow-off fadeInUp" data-wow-delay="400ms" data-wow-duration="1200ms">
                    <h2 class="main-title title-gradient-animation no no middle ">Connect With {!! siteNameHtml() !!}</h2>
                </div>
                <div class="description wow-off fadeInUp" data-wow-delay="600ms" data-wow-duration="1200ms">
                    <p style="color:#7A7A7A;">We're here to help with fast, secure, and affordable money transfers worldwide. Contact us for assistance with international payments, exchange rate inquiries, or any questions about sending money globally. Our dedicated global support team is available 24/7.</p>
                </div>
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-12">
                        <div class="rt-contact-infoj wow-off fadeInUp" data-wow-delay="200ms" data-wow-duration="1200ms">
                            <div class="content-holder">
                                <h3 class="info-title">Global Headquarters</h3>
                                <ul class="contact-list list-vertical">
                                    <li class="list">{!! siteNameHtml() !!}<br>Calgary, AB, Canada</li>
                                    <li class="list">
                                        <a href="mailto:support@kinmoney.ca">support@kinmoney.ca</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="contact-form">
                    <div class="title">
                        <h3>Get In Touch</h3>
                    </div>
                    <form action="{{route('contact.send')}}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" id="name" class="form-control" required="" data-error="Please enter your name">
                                    @error('name')<span class="text-danger">{{$message}}</span>@enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="email" name="email" id="email" class="form-control" required="" data-error="Please enter your email">
                                    @error('email')<span class="text-danger">{{$message}}</span>@enderror
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group">
                                    <label>Subject</label>
                                    <input type="text" name="msg_subject" id="msg_subject" class="form-control" required="" data-error="Please enter your subject">
                                    @error('subject')<span class="text-danger">{{$message}}</span>@enderror
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group">
                                    <label>Message</label>
                                    <textarea name="message" class="form-control" id="message" cols="30" rows="5" required="" data-error="Write your message"></textarea>
                                    @error('message')<span class="text-danger">{{$message}}</span>@enderror
                                </div>
                            </div>
                            <x-turnstile wire:model="cf-turnstile-response" />
                            @if ($errors->has('cf-turnstile-response'))
                                <span class="text-danger">{{ $errors->first('cf-turnstile-response') }}</span>
                            @endif
                            <div class="col-lg-12 col-md-12">
                                <button type="submit" class="btn btn-lg rt-solid-btn-primary" style="pointer-events: all; cursor: pointer;">
                                    Send message
                                    <span></span>
                                </button>
                                
                                <div id="msgSubmit" class="h3 text-center hidden"></div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>