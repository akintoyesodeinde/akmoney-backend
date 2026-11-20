@php
    $notifications = \App\Support\NotificationHelper::get();
@endphp

<div class="flex items-center gap-2 md:gap-3">
    <div class="relative">
    <!-- Bell -->
    <button
        id="notificationBtn"
        class="relative p-2 hover:bg-bg-secondary rounded-lg transition-colors">
        <i class="ri-notification-3-line text-lg text-text-secondary"></i>

        @if ($notifications['unread_count'] > 0)
            <span
                class="absolute top-1 right-1 min-w-[8px] h-2 px-1 bg-brand-gold text-white text-[10px] rounded-full flex items-center justify-center">
                {{ $notifications['unread_count'] }}
            </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div
        id="notificationDropdown"
        class="hidden absolute right-0 mt-2 w-80 max-w-[calc(100vw-1rem)] bg-card-white border border-border-gray rounded-2xl shadow-xl z-50 overflow-hidden"
        style="box-shadow:0 12px 40px -8px rgba(0,0,0,0.18);">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px 12px;border-bottom:1px solid var(--tw-border-border-gray,#e5e7eb);">
            <span style="font-size:14px;font-weight:700;color:inherit;line-height:1;">Notifications</span>
            @if ($notifications['unread_count'] > 0)
            <span style="font-size:10px;font-weight:700;background:#f59e0b;color:#fff;padding:2px 7px;border-radius:999px;line-height:1.4;">{{ $notifications['unread_count'] }} new</span>
            @endif
        </div>

        {{-- Items --}}
        <div style="max-height:384px;overflow-y:auto;">
            @forelse ($notifications['items'] as $note)
            @php $d = $note->display; @endphp
            <div style="padding:12px 16px;border-bottom:1px solid rgba(0,0,0,0.05);display:flex;align-items:flex-start;gap:10px;{{ $note->is_read ? 'opacity:0.55;' : '' }}background:transparent;" class="hover:bg-bg-secondary transition-colors">
                {{-- Icon --}}
                <div style="flex-shrink:0;width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:{{ $d['color'] }}1a;">
                    <i class="{{ $d['icon'] }}" style="font-size:15px;color:{{ $d['color'] }};"></i>
                </div>
                {{-- Content --}}
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:700;color:inherit;line-height:1.3;margin-bottom:3px;">{{ $d['title'] }}</div>
                    <div style="font-size:12px;color:#64748b;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $d['message'] }}</div>
                    <div style="font-size:10px;color:#94a3b8;margin-top:4px;line-height:1;">{{ $note->created_at->diffForHumans() }}</div>
                </div>
                {{-- Mark read --}}
                @if(!$note->is_read)
                <form method="POST" action="{{ route('user.notifications.read') }}" style="flex-shrink:0;">
                    @csrf
                    <input type="hidden" name="id" value="{{ $note->id }}">
                    <button type="submit" title="Mark as read"
                        style="width:26px;height:26px;border-radius:6px;border:none;background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#94a3b8;"
                        class="hover:bg-bg-secondary hover:text-brand-gold transition-colors">
                        <i class="ri-check-line" style="font-size:13px;"></i>
                    </button>
                </form>
                @endif
            </div>
            @empty
                <div style="padding:40px 16px;text-align:center;">
                    <i class="ri-notification-off-line" style="font-size:28px;color:#cbd5e1;display:block;margin-bottom:8px;"></i>
                    <div style="font-size:13px;color:#94a3b8;">No notifications yet</div>
                </div>
            @endforelse
        </div>

        {{-- Footer: mark all --}}
        @if ($notifications['unread_count'] > 0)
            <div style="padding:10px 16px;border-top:1px solid rgba(0,0,0,0.05);text-align:center;">
                <form method="POST" action="{{ route('user.notifications.read') }}">
                    @csrf
                    <button style="font-size:12px;font-weight:600;color:#0d9488;background:none;border:none;cursor:pointer;font-family:inherit;" class="hover:underline">
                        Mark all as read
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>



    <div class="relative group">
        <button
            id="profileBtn"
            class="hidden md:flex items-center gap-3 pl-4 border-l border-border-gray cursor-pointer hover:bg-bg-secondary rounded-lg px-3 py-2 transition-colors">
            <div class="text-right">
                <p class="text-sm font-semibold text-text-primary">{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</p>
                <p class="text-xs text-text-secondary">{{ auth()->user()->mobileNumber }}</p>
            </div>
            <img
                src="{{ asset(auth()->user()->image ? 'assets/images/user/profile/'.auth()->user()->image : 'assets/images/avatar.png') }}"
                alt="{{ auth()->user()->firstname }}"
                class="w-10 h-10 rounded-full object-cover shadow-md" />
            <i class="ri-arrow-down-s-line text-text-secondary"></i>
        </button>

        <!-- Dropdown Menu -->
        <div id="profileDropdown" class="hidden group-hover:block md:absolute right-0 mt-2 w-64 bg-card-white border border-border-gray rounded-2xl shadow-lg z-50">
            <!-- User Info Header -->
            <div class="p-4 border-b border-border-light">
                <div class="flex items-center gap-3">
                    <img
                        src="{{ asset(auth()->user()->image ? 'assets/images/user/profile/'.auth()->user()->image : 'assets/images/avatar.png') }}"
                        alt="{{ auth()->user()->firstname }}"
                        class="w-12 h-12 rounded-full object-cover" />
                    <div>
                        <p class="text-sm font-semibold text-text-primary">{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</p>
                        <p class="text-xs text-text-secondary">{{ auth()->user()->mobileNumber }}</p>
                    </div>
                </div>
            </div>

            <!-- Menu Items -->
            <div class="py-2">
                <a href="{{ route('user.profile.setting') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-text-primary hover:bg-bg-secondary transition-colors">
                    <i class="ri-user-line text-text-secondary"></i>
                    <span>Your details</span>
                </a>
                <a href="{{ route('user.profile.setting') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-text-primary hover:bg-bg-secondary transition-colors">
                    <i class="ri-settings-3-line text-text-secondary"></i>
                    <span>Account settings</span>
                </a>
                <a href="{{ route('user.logout') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-text-primary hover:bg-bg-secondary transition-colors">
                    <i class="ri-logout-box-r-line text-text-secondary"></i>
                    <span>Log out</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Profile Picture with Click Menu -->
    <button
        id="mobileProfileBtn"
        class="md:hidden w-8 h-8 rounded-full object-cover shadow-sm shadow-gray-300 cursor-pointer overflow-hidden">
        <img
            src="{{ asset(auth()->user()->image ? 'assets/images/user/profile/'.auth()->user()->image : 'assets/images/avatar.png') }}"
            alt="{{ auth()->user()->firstname }}"
            class="w-full h-full object-cover" />
    </button>

    <!-- Mobile Dropdown Menu -->
    <div id="mobileProfileDropdown" class="hidden fixed inset-0 md:hidden z-50">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/20"></div>

        <!-- Menu -->
        <div class="absolute bottom-0 left-0 right-0 bg-card-white rounded-t-3xl shadow-lg animate-slide-up">
            <!-- User Info Header -->
            <div class="p-4 border-b border-border-light">
                <div class="flex items-center gap-3">
                    <img
                        src="{{ asset(auth()->user()->image ? 'assets/images/user/profile/'.auth()->user()->image : 'assets/images/avatar.png') }}"
                        alt="{{ auth()->user()->firstname }}"
                        class="w-12 h-12 rounded-full object-cover" />
                    <div>
                        <p class="text-sm font-semibold text-text-primary">{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</p>
                        <p class="text-xs text-text-secondary">{{ auth()->user()->mobileNumber }}</p>
                    </div>
                </div>
            </div>

            <!-- Theme Toggle Buttons -->
            <div class="px-4 py-3 border-b border-border-light flex gap-3">
                <form method="POST" action="@php echo $_SERVER['REQUEST_URI']; @endphp" class="flex-1">
                    <input type="hidden" name="theme_mode" value="light">
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-border-gray transition-all <?php echo (auth()->user()->current_mode === 'light') ? 'bg-brand-teal text-white border-brand-teal' : 'bg-bg-secondary text-text-primary hover:bg-bg-tertiary'; ?>">
                        <i class="ri-sun-line"></i>
                        <span class="text-sm font-medium">Light</span>
                    </button>
                </form>
                <form method="POST" action="@php echo $_SERVER['REQUEST_URI']; @endphp" class="flex-1">
                    <input type="hidden" name="theme_mode" value="dark">
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-border-gray transition-all <?php echo (auth()->user()->current_mode === 'dark') ? 'bg-brand-teal text-white border-brand-teal' : 'bg-bg-secondary text-text-primary hover:bg-bg-tertiary'; ?>">
                        <i class="ri-moon-line"></i>
                        <span class="text-sm font-medium">Dark</span>
                    </button>
                </form>
            </div>

            <!-- Menu Items -->
            <div class="py-2">
                <a href="{{ route('user.profile.setting') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-text-primary hover:bg-bg-secondary transition-colors">
                    <i class="ri-user-line text-text-secondary"></i>
                    <span>Your details</span>
                </a>
                <a href="{{ route('user.profile.setting') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-text-primary hover:bg-bg-secondary transition-colors">
                    <i class="ri-settings-3-line text-text-secondary"></i>
                    <span>Account settings</span>
                </a>
                <a href="{{ route('user.logout') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-text-primary hover:bg-bg-secondary transition-colors">
                    <i class="ri-logout-box-r-line text-text-secondary"></i>
                    <span>Log out</span>
                </a>
            </div>

        </div>
    </div>
</div>

<style>
    @keyframes slideUp {
        from {
            transform: translateY(100%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .animate-slide-up {
        animation: slideUp 0.3s ease-out;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('notificationBtn');
    const dropdown = document.getElementById('notificationDropdown');

    if (!btn || !dropdown) return;

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', function () {
        dropdown.classList.add('hidden');
    });

    dropdown.addEventListener('click', function (e) {
        e.stopPropagation();
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile profile button
        const mobileProfileBtn = document.getElementById('mobileProfileBtn');
        const mobileProfileDropdown = document.getElementById('mobileProfileDropdown');
        const backdrop = mobileProfileDropdown.querySelector('.absolute.inset-0');

        // Open mobile menu
        mobileProfileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            mobileProfileDropdown.classList.remove('hidden');
        });

        // Close mobile menu on backdrop click
        backdrop.addEventListener('click', function() {
            mobileProfileDropdown.classList.add('hidden');
        });

        // Close mobile menu when clicking menu items
        const mobileMenuItems = mobileProfileDropdown.querySelectorAll('a');
        mobileMenuItems.forEach(item => {
            item.addEventListener('click', function() {
                mobileProfileDropdown.classList.add('hidden');
            });
        });

        // Prevent dropdown from closing when interacting with it
        const mobileMenu = mobileProfileDropdown.querySelector('.absolute.bottom-0');
        mobileMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
</script>