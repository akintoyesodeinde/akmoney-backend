@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="max-w-md mx-auto py-6 md:py-10 px-2 animate-in slide-in-from-right-10 duration-500">
    <div class="bg-card-white rounded-[32px] md:rounded-[40px] p-6 md:p-10 border border-border-gray shadow-xl">

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-brand-gold/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ri-shield-user-fill text-brand-gold text-3xl"></i>
            </div>
            <h2 class="text-xl md:text-2xl font-extrabold text-text-primary">Reset PIN</h2>
            <p class="text-text-secondary text-xs md:text-sm mt-2">
                Answer Security Question to Reset Pin
            </p>
        </div>

        <form action="#" class="space-y-6">
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-text-secondary uppercase ml-1 tracking-widest">Verify Email</label>
                <div class="relative">
                    <input type="email" value="{{auth()->user()->email}}" readonly
                        class="w-full px-4 py-4 bg-bg-main border border-border-gray rounded-2xl text-sm font-medium text-text-secondary cursor-not-allowed">
                    <i class="ri-lock-fill absolute right-4 top-1/2 -translate-y-1/2 text-text-secondary/40"></i>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-text-secondary uppercase ml-1 tracking-widest">Security Question</label>
                <div class="p-4 bg-bg-main/50 border border-border-gray rounded-2xl">
                    <p class="text-xs font-bold text-text-primary mb-2">What was the name of your first pet?</p>
                    <input type="text" placeholder="Your answer"
                        class="w-full bg-transparent border-b border-border-gray py-1 focus:border-brand-teal outline-none transition-all text-sm uppercase">
                </div>
            </div>

            <div class="space-y-4 pt-4">
                <button type="submit" class="w-full bg-brand-teal text-white font-bold py-4 rounded-2xl shadow-lg shadow-brand-teal/20 hover:bg-opacity-90 active:scale-[0.98] transition-all">
                    Reset Pin
                </button>

                <button type="button" onclick="window.history.back()"
                    class="w-full text-text-secondary font-bold text-xs uppercase tracking-widest hover:text-brand-teal transition-colors">
                    I remembered it
                </button>
            </div>
        </form>
    </div>

    <div class="mt-8 text-center px-6">
        <p class="text-xs text-text-secondary">
            Still having issues? Contact <a href="{{ route('ticket.index') }}" class="text-brand-teal font-bold underline">Customer Support</a>
        </p>
    </div>
</div>
@endsection