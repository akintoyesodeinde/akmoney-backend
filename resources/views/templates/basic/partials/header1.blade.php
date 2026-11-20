<header class="bg-card-white border-b border-border-gray px-4 md:px-6 sticky top-0 z-40">
    <div class="flex items-center justify-between">
        <div class="flex-1 flex items-center gap-3">
            <button
                id="toggle-sidebar"
                class="md:hidden text-text-secondary hover:text-text-primary cursor-pointer mr-1">
                <i class="ri-menu-line text-lg"></i>
            </button>
            <div>
                <h1 class="text-lg md:text-xl font-bold text-text-primary">
                    {{ gs()->siteName(__($pageTitle)) }}
                </h1>
                <?php if (isset($pageSubtitle) && $pageSubtitle): ?>
                    <p class="hidden md:block text-sm text-text-secondary mt-1">
                        <?php echo htmlspecialchars($pageSubtitle); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="hidden md:block">
                @include($activeTemplate . 'partials.theme-toggle')
            </div>
            @include($activeTemplate . 'partials.header_profile')
        </div>
    </div>
</header>