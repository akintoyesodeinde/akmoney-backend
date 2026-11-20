<footer class="overflow-hidden" style="padding: 20px;position: relative;
    top: 30px;">
    <div class="rt-footer-top" style="border-top-left-radius: 20px;border-top-right-radius: 20px;padding-bottom: 2.75rem;">
        <div class="container custom-container">
            <div class="row gy-4 justify-content-between" style="margin-top: -80px;">
                <div class="col-lg-4 text-lg-start text-center">
                    <a href="/">
                        <img style="max-height:60px;width:auto;object-fit:contain;" src="{{ siteLogo('dark') }}" alt="{{ gs('site_name') }}" class="rt-footer-logo">
                    </a>
                </div>
                <div class="col-lg-8">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div class="text-lg-start text-center">
                            <h3 class="mb-1 h6">Get {!! siteNameHtml() !!} Updates</h3>
                            <p class="text-sm mb-0">Stay informed about fast, secure, and affordable international money transfers worldwide</p>
                        </div>
                        <div class="flex-grow-1">
                            <form class="rt-subscribe-form" id="subscribe">
                                <div class="icon-field">
                                    <span class="icon"><i class="ri-mail-line"></i></span>
                                    <input type="text" name="email_sub" class="form-control email" placeholder="Enter Your Email" required="">
                                </div>
                                <button type="submit" aria-label="Subscribe"><i class="ri-send-plane-fill"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row gy-4">

                <div class="col-lg-2 col-6"><h4 class="rt-footer-title">Quick Links</h4>
                <ul class="rt-footer-menu">
                    <li><a href="/">Home</a></li>
                    <li><a href="{{route('login')}}">Login</a></li>
                    <li><a href="{{route('register')}}">Signup</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-6">
                <h4 class="rt-footer-title">Support</h4>
                <ul class="rt-footer-menu">
                    <li><a href="{{url('/faq')}}">FAQs</a></li>
                    <li><a href="{{url('contact')}}">Contact us</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-6">
                <h4 class="rt-footer-title">Company</h4>
                  <ul class="rt-footer-menu">
                      <li><a href="{{url('/blog')}}">Blog</a></li>
                      <li><a href="{{url('/about')}}">Our Story</a></li>
                  </ul>
            </div>
          
            <div class="col-lg-2 col-6">
                <h4 class="rt-footer-title">Legal</h4>
                <ul class="rt-footer-menu">
                    <li><a href="{{ route('compliance') }}">Compliance</a></li>
                    <li><a href="/terms-conditions">Terms &amp; conditions</a></li>
                    <li><a href="/privacy-policy">Privacy Policy</a></li>
                    <li><a href="/cookie-policy">Cookies Policy</a></li>
                </ul>
            </div>              
                <div class="col-lg-3 ms-auto">
                    <h4 class="rt-footer-title" style="font-size: 17px;">{!! siteNameHtml() !!} Mobile App</h4>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <a href="#">
                            <img src="{{asset('new_assetts/google.jpg')}}" alt="Get on Google Play" class="rt-app-btn">
                        </a>
                        <a href="#">
                            <img src="{{asset('new_assetts/appstore.jpg')}}" alt="Download on App Store" class="rt-app-btn">
                        </a>
                    </div>

                    <h4 class="rt-footer-title mt-4" style="font-size: 17px;">Connect With Us</h4>
                    <ul class="d-flex flex-wrap align-items-center gap-3">
                        <li><a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a></li>
                        <li><a href="#" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a></li>
                        <li><a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a></li>
                        <li><a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a></li>
                    </ul>
                </div>
            </div>
            <hr>
            {{-- Compliance Badges --}}
            <div class="d-flex flex-wrap justify-content-center gap-3 py-4">
                <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3" style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.15);">
                    <i class="ri-government-line" style="font-size:1.1rem;"></i>
                    <div style="line-height:1.2">
                        <div style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;opacity:.65;">FINTRAC</div>
                        <div style="font-size:0.72rem;font-weight:700;">Registered MSB</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3" style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.15);">
                    <i class="ri-shield-keyhole-line" style="font-size:1.1rem;"></i>
                    <div style="line-height:1.2">
                        <div style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;opacity:.65;">256-bit SSL</div>
                        <div style="font-size:0.72rem;font-weight:700;">Secure &amp; Encrypted</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3" style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.15);">
                    <i class="ri-file-shield-2-line" style="font-size:1.1rem;"></i>
                    <div style="line-height:1.2">
                        <div style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;opacity:.65;">Anti-Money Laundering</div>
                        <div style="font-size:0.72rem;font-weight:700;">AML Compliant</div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
               <h6 class="text-sm">Important Reminder</h6>
                <p class="text-sm">Please verify all recipient details before sending, as {!! siteNameHtml() !!} cannot reverse completed transfers. Exchange rates and fees are subject to change - confirm the final amount before proceeding. All transactions are protected with industry-leading encryption for your security.</p>
            </div>
        </div>
    </div>
    <div class="rt-footer-bottom" style="border-top: 1px solid transparent;background:#0b0909;">
        <div class="container">
            <p style="color:#fff;" class="text-center mb-0">&copy; {{ date('Y') }} {!! siteNameHtml() !!}. All rights reserved.</p>
        </div>
    </div>
</footer>