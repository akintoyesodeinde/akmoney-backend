<div class="sidebar-content-safe">
    <div class="close-btn" style="text-align: right; padding: 10px; cursor: pointer;">
        <i class="fa-solid fa-xmark"></i>
    </div>

    <div class="sidebar-logo" style="text-align: center; margin-bottom: 20px;">
        <a class="logo logo-light" href="{{ route('home') }}">
            <img alt="{{ gs('site_name') }}" class="img-fluid logo__is" src="{{ siteLogo() }}" />
        </a>
    </div>

    <ul>
        <li class="{{ request()->routeIs('user.home') ? 'active' : '' }}">
            <a href="{{ route('user.home') }}">
                <i class="fa-solid fa-chart-line"></i>
                <span style="padding-left:5px;">Dashboard</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('user.send.money.history') ? 'active' : '' }}">
            <a href="{{ route('user.send.money.history') }}">
                <i class="fa-solid fa-arrow-right-arrow-left"></i>
                <span style="padding-left:5px;">Transactions</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('user.send.money.now') ? 'active' : '' }}">
            <a href="{{ route('user.send.money.now') }}">
                <i class="fa-solid fa-paper-plane"></i>
                <span style="padding-left:5px;">Send Money</span>
            </a>
        </li>
    </ul>

    <ul class="bottom-item">
        <li class="{{ request()->routeIs('user.profile.setting') ? 'active' : '' }}">
            <a href="{{ route('user.profile.setting') }}">
                <i class="fa-regular fa-circle-user"></i>
                <span style="padding-left:5px;">Account</span>
            </a>
        </li>
        <li>
            <a href="{{ route('user.twofactor') }}">
                <i class="fa-solid fa-shield-halved"></i>
                <span style="padding-left:5px;">2FA Security</span>
            </a>
        </li>
        <li>
            <a href="{{ route('user.change.password') }}">
                <i class="fas fa-key"></i>
                <span style="padding-left:5px;">Change Password</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('ticket.index') ? 'active' : '' }}">
            <a href="{{ route('ticket.index') }}">
                <i class="fa-solid fa-headset"></i>
                <span style="padding-left:5px;">Support</span>
            </a>
        </li>
        <li>
            <a href="{{ route('user.logout') }}">
                <i class="fas fa-sign-out"></i>
                <span style="padding-left:5px;">Quit</span>
            </a>
        </li>
    </ul>

    <div class="referral-container">
        <div class="referral-card">
            <div class="referral-header">
                <h4 class="referral-title">📨 Invite Friends</h4>
                <p class="referral-subtitle">Share your link and earn rewards</p>
            </div>
            
            <div class="referral-content">
                <div class="referral-actions">
                    <div class="referral-input-container">
                        <p class="input-label">Your Referral Link</p>
                        <div class="input-wrapper">
                            <input type="text" value="{{ route('home') }}?reference={{ auth()->user()->username }}" class="referral-input" readonly id="referralLink">
                            <button type="button" class="copy-btn" id="copyBoard">
                                <span class="copy-icon">📋</span>
                                <span class="copy-text">Copy</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .sidebar-content-safe {
        padding: 20px;
        height: 100%;
        display: flex;
        flex-direction: column; 
        
    }
    .sidebar-content-safe ul {
        padding: 0;
        list-style: none;
        margin-bottom: 20px; 
        
    }
    .sidebar-content-safe ul li {
        margin-bottom: 10px; 
        
    }
    .sidebar-content-safe ul li a {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        color: #555;
        border-radius: 8px;
        text-decoration: none;
        transition: 0.3s;
        font-weight: 500; 
        
    }
    .sidebar-content-safe ul li a:hover, .sidebar-content-safe ul li.active a {
        background-color: #f3f4f6;
        color: #5732C6; 
    }
    
    .referral-container {
        margin-top: auto;
        padding-bottom: 20px; 
    }
    .referral-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        border: 1px solid #e1e8f0;
        overflow: hidden; 
    }
    .referral-header {
        padding: 15px;
        background: linear-gradient(135deg, #ffb000 0%, #ffb000ab 100%);
        color: white;
        text-align: center; 
    }
    .referral-title {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: white;
    }
    .referral-subtitle {
        font-size: 12px;
        margin: 0;
        opacity: 0.9;
        color: white; 
    }
    .referral-content {
        padding: 15px;
    }
    .referral-input {
        width: 100%;
        padding: 8px;
        border: 1px solid #cbd5e0;
        border-radius: 8px;
        font-size: 12px;
        margin-bottom: 10px; 
        
    }
    .copy-btn {
        width: 100%;
        padding: 10px;
        background: #ffb000;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer; 
        
    }
    /* Large screen collapsible sidebar */
@media (min-width: 1024px) {
    #my-fixed-sidebar.collapsed {
        width: 80px; /* only icons remain */
        overflow: hidden;
    }

    #my-fixed-sidebar.collapsed .sidebar-content-safe ul li a span,
    #my-fixed-sidebar.collapsed .referral-container {
        display: none; /* hide text and referral section */
    }

    #my-fixed-sidebar.collapsed .sidebar-logo img {
        width: 40px; /* optional: shrink logo */
    }

    #my-fixed-header.collapsed {
        left: 80px;
        width: calc(100% - 80px);
    }

    #my-fixed-body.collapsed {
        margin-left: 80px;
    }

    /* Smooth transitions */
    #my-fixed-sidebar,
    #my-fixed-header,
    #my-fixed-body {
        transition: all 0.3s ease;
    }
}

</style>

<script src="{{ asset('assets1/global/js/jquery-3.7.1.min.js') }}"></script>

<script>
    (function($){
        "use strict";
        $('#copyBoard').on('click', function () {
            var copyText = document.getElementById("referralLink");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            $(this).find('.copy-text').text('Copied!');
            setTimeout(() => { $(this).find('.copy-text').text('Copy'); }, 1500);
        });
    })(jQuery);
</script>