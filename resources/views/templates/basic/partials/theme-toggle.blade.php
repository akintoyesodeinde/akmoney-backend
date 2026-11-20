<!-- Theme Toggle Button -->
<!--<button id="theme-toggle" class="relative inline-flex items-center justify-center w-10 h-10 rounded-xl bg-bg-secondary hover:bg-bg-tertiary transition-all duration-200 group" title="Toggle theme">-->
    <!-- Light mode icon -->
<!--    <svg id="theme-icon-light" class="w-5 h-5 text-text-secondary group-hover:text-text-primary transition-all duration-200 <?php echo (auth()->user()->current_mode === 'dark') ? 'opacity-0 scale-75' : 'opacity-100 scale-100'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
<!--        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>-->
<!--    </svg>-->

    <!-- Dark mode icon -->
<!--    <svg id="theme-icon-dark" class="absolute w-5 h-5 text-text-secondary group-hover:text-text-primary transition-all duration-200 <?php echo (auth()->user()->current_mode === 'dark') ? 'opacity-100 scale-100' : 'opacity-0 scale-75'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
<!--        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>-->
<!--    </svg>-->
<!--</button>-->



<div class="flex gap-3 px-4 py-3">
    <!-- Light Mode -->
    @if(auth()->user()->current_mode === 'dark')
    <form method="POST" action="{{ route('user.theme.update') }}" class="flex-1">
        @csrf
        <input type="hidden" name="theme_mode" value="light">
        <button type="submit"
            class="relative inline-flex items-center justify-center w-10 h-10 rounded-xl bg-bg-secondary hover:bg-bg-tertiary transition-all duration-200 group">
            <svg id="theme-icon-light" class="w-5 h-5 text-text-secondary group-hover:text-text-primary transition-all duration-200 <?php echo (auth()->user()->current_mode === 'light') ? 'opacity-0 scale-75' : 'opacity-100 scale-100'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
    </svg>
        </button>
    </form>
    @else
    <!-- Dark Mode -->
    <form method="POST" action="{{ route('user.theme.update') }}" class="flex-1">
        @csrf
        <input type="hidden" name="theme_mode" value="dark">
        <button type="submit"
            class="relative inline-flex items-center justify-center w-10 h-10 rounded-xl bg-bg-secondary hover:bg-bg-tertiary transition-all duration-200 group">
            <svg id="theme-icon-dark" class="absolute w-5 h-5 text-text-secondary group-hover:text-text-primary transition-all duration-200 <?php echo (auth()->user()->current_mode === 'light') ? 'opacity-100 scale-100' : 'opacity-0 scale-75'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>        
        </button>
    </form>
    @endif
</div>
