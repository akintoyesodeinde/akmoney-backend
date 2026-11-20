<div id="sidebar-overlay"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 -translate-x-full md:translate-x-0 md:opacity-0 md:pointer-events-none transition-all duration-300">
</div>
<style>
#sidebar::-webkit-scrollbar { display: none; }
#sidebar { scrollbar-width: none; -ms-overflow-style: none; }
</style>
<div id="sidebar"
    class="fixed left-0 h-screen bg-card-white border-r border-border-gray transition-all duration-300 w-56 z-50 -translate-x-full md:translate-x-0 overflow-hidden flex flex-col">
    <div class="flex items-center justify-between px-4 md:py-2 border-b border-border-gray">
        <a href="{{ url('/') }}" class="flex items-center py-3 no-underline" style="text-decoration:none;">
            <img src="{{ siteLogo() }}" alt="{{ gs('site_name') }}" style="max-height:36px;width:auto;max-width:140px;object-fit:contain;">
        </a>
        <button id="close-sidebar" class="md:hidden text-text-secondary hover:text-text-primary cursor-pointer">
            <i class="ri-close-line text-lg"></i>
        </button>
    </div>
    <nav class="p-3 flex-1 overflow-y-auto">
        <ul class="space-y-1">
            <li>
                <a href="{{ route('user.home') }}">
                    <button
                        class="nav-link w-full flex items-center gap-3 px-3 py-2 rounded-xl transition-all cursor-pointer whitespace-nowrap text-text-secondary hover:bg-bg-secondary"
                        data-page="dashboard">
                        <i class="ri-dashboard-fill text-lg"></i>
                        <span class="font-medium text-sm">Dashboard</span>
                    </button>
                </a>
            </li>
            <li>
                <!--<a href="{{ route('user.send.money.history') }}">-->
                <a href="{{ route('user.transactions') }}">
                <button
                    class="nav-link w-full flex items-center gap-3 px-3 py-2 rounded-xl transition-all cursor-pointer whitespace-nowrap text-text-secondary hover:bg-bg-secondary"
                    data-page="transactions">
                    <i class="ri-arrow-left-right-line text-lg"></i>
                    <span class="font-medium text-sm">Transactions</span>
                </button>
                </a>
            </li>
            @if(gs('wallet_module'))
            <li>
                <a href="{{ route('user.wallets') }}">
                <button
                    class="nav-link w-full flex items-center gap-3 px-3 py-2 rounded-xl transition-all cursor-pointer whitespace-nowrap text-text-secondary hover:bg-bg-secondary"
                    data-page="wallets">
                    <i class="ri-wallet-3-fill text-lg"></i>
                    <span class="font-medium text-sm">Wallets</span>
                </button>
                </a>
            </li>
            @endif
            <li>
                <a href="{{ route('user.send.money.now') }}">
                <button
                    class="nav-link w-full flex items-center gap-3 px-3 py-2 rounded-xl transition-all cursor-pointer whitespace-nowrap text-text-secondary hover:bg-bg-secondary"
                    data-page="send-money">
                    <i class="ri-send-plane-fill text-lg"></i>
                    <span class="font-medium text-sm">Send Money</span>
                </button>
                </a>
            </li>
            <li>
                <a href="{{ route('user.reps') }}">
                <button
                    class="nav-link w-full flex items-center gap-3 px-3 py-2 rounded-xl transition-all cursor-pointer whitespace-nowrap text-text-secondary hover:bg-bg-secondary"
                    data-page="recipients">
                    <i class="ri-contacts-book-2-fill text-lg"></i>
                    <span class="font-medium text-sm">Recipients</span>
                </button>
                </a>
            </li>
            <li>
                <a href="{{ route('user.profile.setting') }}">
                <button
                    class="nav-link w-full flex items-center gap-3 px-3 py-2 rounded-xl transition-all cursor-pointer whitespace-nowrap text-text-secondary hover:bg-bg-secondary"
                    data-page="account">
                    <i class="ri-user-fill text-lg"></i>
                    <span class="font-medium text-sm">Account</span>
                </button>
                </a>
            </li>
            <li>
                <a href="{{ route('user.twofactor') }}">
                <button
                    class="nav-link w-full flex items-center gap-3 px-3 py-2 rounded-xl transition-all cursor-pointer whitespace-nowrap text-text-secondary hover:bg-bg-secondary"
                    data-page="2fa">
                    <i class="ri-shield-check-fill text-lg"></i>
                    <span class="font-medium text-sm">2FA Security</span>
                </button>
                </a>
            </li>
            <li>
                <a href="{{ route('user.change.password') }}">
                <button
                    class="nav-link w-full flex items-center gap-3 px-3 py-2 rounded-xl transition-all cursor-pointer whitespace-nowrap text-text-secondary hover:bg-bg-secondary"
                    data-page="password">
                    <i class="ri-lock-password-fill text-lg"></i>
                    <span class="font-medium text-sm">Change Password</span>
                </button>
                </a>
            </li>
            <li>
                 <a href="{{ route('ticket.index') }}">
                <button
                    class="nav-link w-full flex items-center gap-3 px-3 py-2 rounded-xl transition-all cursor-pointer whitespace-nowrap text-text-secondary hover:bg-bg-secondary"
                    data-page="support">
                    <i class="ri-customer-service-2-fill text-lg"></i>
                    <span class="font-medium text-sm">Support</span>
                </button>
                </a>
            </li>
            <li>
                <a href="{{ route('user.logout') }}">
                <button
                    class="nav-link w-full flex items-center gap-3 px-3 py-2 rounded-xl transition-all cursor-pointer whitespace-nowrap text-text-secondary hover:bg-bg-secondary"
                    data-page="logout">
                    <i class="ri-logout-box-r-fill text-lg"></i>
                    <span class="font-medium text-sm">Quit</span>
                </button>
                </a>
            </li>
        </ul>
    </nav>
      @include($activeTemplate . 'partials.referral')
   
</div>
