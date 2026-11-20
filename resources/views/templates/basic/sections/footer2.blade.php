<div style="background-color:#006d5b;
    padding-top: 30px;
    padding-right: 30px;
    padding-bottom: 0px;
    padding-left:10px; 
    margin-top: 0px;
    margin-right: 20px;
    margin-bottom: -20px;
    height: 430px;
    margin: 67px 20px 20px;
    border-radius: 25px;
    box-shadow: 0 0.375rem 0.75rem rgba(140, 152, 164, .075);
    position:relative;
    top:27px;
">
        <div class="container-fluid">
             <div class="row">
                 <div class="col-md-3" style="margin-top: -16px;">
                     <div class="site-branding light">
                        <a href="{{ url('/') }}" rel="home" aria-label="site logo" class="has-mobile-logo">
                            <img style="margin-left: -25px;max-height:60px;width:auto;object-fit:contain;" src="{{ siteLogo('dark') }}" alt="{{ gs('site_name') }}"/>
                        </a>
                    </div>
                    <div class="rt-contact-info wow-off fadeInUp" data-wow-delay="200ms" data-wow-duration="1200ms">
                        <div class="content-holder">
                            <ul class="contact-list list-vertical">
                                <li class="list" style="font-size: 16px;">
                                    {!! siteNameHtml() !!} is the trusted international remittance platform for fast, secure, and affordable money transfers worldwide. We provide competitive exchange rates and transparent fees for personal and business transfers.
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="rt-social-icon" style="display: inline-flex;
    align-items: center;
    gap: 10px;
    flex-wrap: unset;">
                        <div class="rt-social-item">
                            <a style="width: 30px;
    height: 30px;" class="wow-off fadeInUp" data-wow-delay="200ms" data-wow-duration="1200ms" href="#" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a style="width: 30px;
    height: 30px;" class="wow-off fadeInUp" data-wow-delay="400ms" data-wow-duration="1200ms" href="#" aria-label="Twitter">
                                <i class="fab fa-x-twitter"></i>
                            </a>
                            <a style="width: 30px;
    height: 30px;" class="wow-off fadeInUp" data-wow-delay="600ms" data-wow-duration="1200ms" href="#" aria-label="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a style="width: 30px;
    height: 30px;" class="wow-off fadeInUp" data-wow-delay="800ms" data-wow-duration="1200ms" href="#" aria-label="Pinterest">
                                <i class="fab fa-pinterest-p"></i>
                            </a>
                        </div>
                    </div>
               </div>
                 <div class="col-md-3">
                     <div class="wow-off fadeInUp" data-wow-delay="400ms" data-wow-duration="1200ms">
                        <h2 class="main-titlefooter middle">Company</h2>
                    </div>
                    <nav class="finwave-navigation" role="navigation">
                        <div class="menu-company-container">
                            <ul id="menu-company" class="finwave-navbar">
                                <li><a href="{{url('about')}}">Our Story</a></li>
                                <li><a href="{{url('faq')}}">How It Works</a></li>
                                <li><a href="{{url('blog')}}">Blog</a></li>
                                <li><a href="{{url('contact')}}">Contact</a></li>
                            </ul>
                        </div>
                    </nav>
               </div>
                 <div class="col-md-3">
                         <div class="wow-off fadeInUp" data-wow-delay="400ms" data-wow-duration="1200ms">
                            <h2 class="main-titlefooter middle">Services</h2>
                        </div>
                        <nav class="finwave-navigation" role="navigation">
                                <div class="menu-features-container">
                                    <ul id="menu-features" class="finwave-navbar">
                                        <li><a href="#">Money Transfers</a></li>
                                        <li><a href="#">Bank Deposits</a></li>
                                        <li><a href="#">Cash Pickup</a></li>
                                        <li><a href="#">Mobile Wallets</a></li>
                                        <li><a href="{{url('contact')}}">Business Solutions</a></li>
                                    </ul>
                                </div>
                        </nav>
                   </div>
                 <div class="col-md-3">
                     <div class="wow-off fadeInUp" data-wow-delay="400ms" data-wow-duration="1200ms">
                        <h2 class="main-titlefooterr middle">Locations</h2>
                    </div>
                    <div class="rt-contact-info wow-off fadeInUp" data-wow-delay="200ms" data-wow-duration="1200ms">
                        <div class="content-holder">
                            <ul class="contact-list list-vertical">
                                
                                <li class="list">
                                    <a href="mailto:support@kinmoney.ca">support@kinmoney.ca</a>
                                </li>
                            </ul>
                        </div>
                    </div>
               </div>  
             </div>  
              <div class="row">
                <div class="col-12 d-flex flex-wrap justify-content-center gap-3 py-3" style="border-top:1px solid rgba(255,255,255,0.12);">
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
               <div class="coppy" style="position: relative;
    top: 5px;">
                    <div class="copyright-text" style="position: relative;
    top: -25px;">
                        &copy; {{ date('Y') }} {!! siteNameHtml() !!}. All rights reserved.
                    </div>
                </div>
         </div>
        </div>     
       
    </div>