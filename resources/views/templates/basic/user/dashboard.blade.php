@extends($activeTemplate . 'layouts.master')
@section('content')
<!-- balance card -->
                <div class="w-full flex overflow-x-auto snap-x snap-mandatory gap-5 mb-10 -mx-3 px-3 md:-mx-6 md:px-6 lg:mx-0 lg:px-0 lg:grid lg:grid-cols-3 lg:overflow-visible lg:snap-none py-2">

                    <div class="relative group bg-brand-teal rounded-2xl p-6 shadow-[0_20px_40px_-15px_rgba(8,75,95,0.3)] transition-all duration-500 hover:-translate-y-1 snap-center flex-shrink-0 w-80 lg:w-auto overflow-hidden border border-white/10 animate-fade-in-up stagger-1 hover:animate-shadow-pulse">
                        <div class="absolute inset-0 bg-gradient-to-tr from-white/5 to-transparent pointer-events-none"></div>

                        <div class="relative z-10 flex flex-col justify-between h-full">
                            <div class="flex items-center justify-between mb-5">
                                <div class="flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 rounded-full bg-brand-gold shadow-[0_0_8px_#ffb000]"></div>
                                    <span class="text-[10px] uppercase tracking-[0.25em] text-white/60 font-bold">{!! siteNameHtml() !!}</span>
                                </div>
                                <i class="ri-medal-2-line text-brand-gold opacity-80 text-xl"></i>
                            </div>

                            <div>
                                <p class="text-[11px] text-white/50 uppercase tracking-widest mb-1">Available Balance</p>
                                <div class="flex items-baseline gap-1">
                                    <h3 class="text-3xl font-bold text-white tracking-tight">{{showAmount(auth()->user()->balance)}}<span class="text-lg text-white/40 font-light"></span></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative group bg-white rounded-2xl p-6 shadow-sm transition-all duration-500 snap-center flex-shrink-0 w-80 lg:w-auto overflow-hidden border border-slate-200 hover:border-[#084b5f]/30 animate-fade-in-up stagger-2 hover:scale-105">
                        <div class="relative z-10 flex flex-col justify-between h-full">
                            <div class="flex items-center justify-between mb-5">
                                <span class="text-[10px] uppercase tracking-[0.25em] text-slate-400 font-bold">Processing</span>
                                <div class="w-8 h-8 rounded-lg bg-brand-teal/5 flex items-center justify-center">
                                    <i class="ri-history-line text-brand-teal text-lg"></i>
                                </div>
                            </div>

                            <div>
                                <p class="text-[11px] text-slate-500 uppercase tracking-widest mb-1 font-medium">Pending Payments</p>
                                <div class="flex items-baseline gap-1">
                                    <h3 class="text-3xl font-bold text-brand-teal tracking-tight">{{ showAmount($widget['payment_pending']) }}<span class="text-lg text-slate-300 font-light"></span></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative group bg-slate-50 rounded-2xl p-6 shadow-inner transition-all duration-500 snap-center flex-shrink-0 w-80 lg:w-auto overflow-hidden border border-slate-200 animate-fade-in-up stagger-3">
                        <div class="relative z-10 flex flex-col justify-between h-full">
                            <div class="flex items-center justify-between mb-5">
                                <span class="text-[10px] uppercase tracking-[0.25em] text-slate-400 font-bold">Settled</span>
                                <i class="ri-checkbox-circle-fill text-brand-teal opacity-40 text-xl"></i>
                            </div>

                            <div>
                                <p class="text-[11px] text-slate-500 uppercase tracking-widest mb-1 font-medium">Completed Payments</p>
                                <div class="flex items-baseline gap-1">
                                    <h3 class="text-3xl font-bold text-slate-900 tracking-tight">{{ showAmount($widget['send_money_amount']) }}<span class="text-lg text-slate-300 font-light"></span></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 md:gap-4 mb-6">
                    <div class="space-y-3 md:space-y-4 grid lg:col-span-2">
                        <div>
                            <h2 class="text-text-primary font-semibold text-lg md:text-xl ml-2 mb-3 animate-fade-in-up">Quick Actions</h2>
                            <div id="quick-actions"
                                class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-2 md:gap-4 mb-4 md:mb-6">
                                <a href="{{ route('user.send.money.now') }}">
                                <button 
                                    class="bg-card-white rounded-xl md:rounded-2xl p-4 md:p-5 shadow-sm hover:shadow-md transition-all text-center animate-fade-in-up stagger-1 hover:scale-110 hover:-translate-y-2 transform">
                                    <div
                                        class="w-10 md:w-12 h-10 md:h-12 rounded-lg md:rounded-xl bg-brand-teal/10 flex items-center justify-center mx-auto mb-2 md:mb-3 animate-bounce-light">
                                        <i class="fa-solid fa-paper-plane text-brand-teal text-lg md:text-xl"></i>
                                    </div>
                                    <div class="text-xs md:text-sm font-medium text-text-primary">Send Money</div>
                                </button>
                                </a>
                                <a href="{{ route('user.transactions') }}">
                                <button 
                                    class="bg-card-white rounded-xl md:rounded-2xl p-4 md:p-5 shadow-sm hover:shadow-md transition-all text-center animate-fade-in-up stagger-2 hover:scale-110 hover:-translate-y-2 transform">
                                    <div
                                        class="w-10 md:w-12 h-10 md:h-12 rounded-lg md:rounded-xl bg-brand-gold/10 flex items-center justify-center mx-auto mb-2 md:mb-3 animate-bounce-light">
                                        <i class="fa-solid fa-receipt text-brand-gold text-lg md:text-xl"></i>
                                    </div>
                                    <div class="text-xs md:text-sm font-medium text-text-primary">Transactions</div>
                                </button>
                                </a>
                                <a href="{{ route('user.reps') }}">
                                <button
                                    class="bg-card-white rounded-xl md:rounded-2xl p-4 md:p-5 shadow-sm hover:shadow-md transition-all text-center">
                                    <div
                                        class="w-10 md:w-12 h-10 md:h-12 rounded-lg md:rounded-xl bg-emerald-100 flex items-center justify-center mx-auto mb-2 md:mb-3 animate-bounce-light">
                                        <i class="fa-solid fa-user text-emerald-600 text-lg md:text-xl"></i>
                                    </div>
                                    <div class="text-xs md:text-sm font-medium text-text-primary">Recipients</div>
                                </button>
                                </a>
                               <a href="/ticket">
                                <button
                                    class="bg-card-white rounded-xl md:rounded-2xl p-4 md:p-5 shadow-sm hover:shadow-md transition-all text-center animate-fade-in-up stagger-4 hover:scale-110 hover:-translate-y-2 transform">
                                    <div
                                        class="w-10 md:w-12 h-10 md:h-12 rounded-lg md:rounded-xl bg-[#A71B47]/10 flex items-center justify-center mx-auto mb-2 md:mb-3 animate-bounce-light">
                                        <i class="fa-solid fa-circle-exclamation text-[#A71B47] text-lg md:text-xl"></i>
                                    </div>
                                    <div class="text-xs md:text-sm font-medium text-text-primary">Complaints</div>
                                </button>
                                </a>
                            </div>


                        </div>

                        <div id="exchange-rates" class="bg-card-white rounded-xl md:rounded-2xl p-4 md:p-6 shadow-sm animate-fade-in-up stagger-5\">
                            <div class="flex items-center justify-between mb-3 md:mb-5">
                                <h2 class="text-base md:text-lg font-semibold text-text-primary">Exchange Rates</h2>
                                <!--<button-->
                                    <!--class="text-brand-teal text-xs md:text-sm font-medium hover:underline">More</button>-->
                            </div>
                            <div id="exchange-rates-wrapper" class="max-h-[320px] overflow-y-auto pr-2 custom-scrollbar">
                                <div class="space-y-2 md:space-y-4">
                                    @foreach ($countries as $c)
                                        <div
                                        class="flex items-center justify-between p-2 md:p-3 rounded-lg md:rounded-xl bg-bg-main text-sm">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 rounded-full bg-[#A71B47]/10 flex items-center justify-center mr-3 overflow-hidden">
                                                <img src="{{$c->image_url}}" alt="{{$c->currency}}" class="w-full h-full object-cover rounded-full">
                                                <!--<span class="text-sm font-semibold text-blue-700">€</span>-->
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-text-primary">CAD / {{$c->currency}}</div>
                                                <div class="text-xs text-text-secondary">{{$c->name}}</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-semibold text-text-primary">{{ number_format($c->rate, 2) }}</div>
                                            <!--<div class="text-xs text-green-600">+0.12%</div>-->
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div id="recent-transactions"
                            class="bg-card-white rounded-xl md:rounded-2xl p-4 md:p-6 shadow-sm max-h-[60vh] md:max-h-[80vh] overflow-y-auto -webkit-scrollbar animate-fade-in-up stagger-1">
                            <div>
                                <div class="flex items-center justify-between mb-3 md:mb-5">
                                    <h2 class="text-base md:text-lg font-semibold text-text-primary animate-slide-in-left">Recent Transactions
                                    </h2>
                                    <a href="{{ route('user.transactions') }}" class="text-brand-teal text-xs md:text-sm font-medium hover:underline hover:scale-110 transition-transform">View
                                        All</a>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-border-gray">
                                                <th
                                                    class="text-left py-2 md:py-3 px-2 text-xs font-semibold text-text-secondary uppercase tracking-wider">
                                                    Recipient</th>
                                                <th
                                                    class="hidden md:table-cell text-left py-2 md:py-3 px-2 text-xs font-semibold text-text-secondary uppercase tracking-wider">
                                                    Date</th>
                                                <th
                                                    class="hidden lg:table-cell text-left py-2 md:py-3 px-2 text-xs font-semibold text-text-secondary uppercase tracking-wider">
                                                    Time</th>
                                                <th
                                                    class="text-left py-2 md:py-3 px-2 text-xs font-semibold text-text-secondary uppercase tracking-wider">
                                                    Status</th>
                                                <th
                                                    class="text-right py-2 md:py-3 px-2 text-xs font-semibold text-text-secondary uppercase tracking-wider">
                                                    Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border-gray text-xs md:text-sm">
                                            @forelse ($transfers as $transfer)
                                            <tr class="hover:bg-bg-main transition-all">
                                                <td class="py-2 md:py-4 px-2">
                                                    <div>
                                                        <div class="font-medium text-text-primary">{{ $transfer->recipient->name }}
                                                        </div>
                                                        <div class="text-xs text-text-secondary hidden md:block">
                                                            {{ __(@$transfer->recipientCountry->name) }}</div>
                                                    </div>
                                                </td>
                                                <td class="hidden md:table-cell py-2 md:py-4 px-2 text-text-secondary">
                                                    {{ showDateTime($transfer->created_at) }}</td>
                                                <td class="hidden lg:table-cell py-2 md:py-4 px-2 text-text-secondary">
                                                    {{ showDateTime($transfer->created_at) }}</td>
                                                @php
                                                $badge = $transfer->statusBadge
                                                @endphp
                                                <td class="py-2 md:py-4 px-2">
                                                    <span class="inline-flex items-center px-2 md:px-3 py-1 rounded-full text-xs font-medium
                                                        {{ $badge['bg'] }} {{ $badge['textColor'] }}">
                                                        
                                                        <span class="w-1 h-1 rounded-full mr-1 {{ $badge['dot'] }}"></span>
                                                        <span class="max-sm:text-[10px]">{{ $badge['text'] }}</span>
                                                    </span>
                                                </td>
                                                <td
                                                    class="py-2 md:py-4 px-2 text-right font-semibold text-text-primary">
                                                    -{{ showAmount($transfer->sending_amount, currencyFormat: false) }}</td>
                                            </tr>
                                            @empty
                                            <h6>No transactions yet</h6>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3 md:space-y-4">
                         <!-- Track Transfer Section -->
                        <div class="bg-gradient-to-br from-brand-teal to-brand-teal/90 rounded-xl md:rounded-2xl p-6 shadow-lg border border-brand-teal/30 animate-fade-in-up stagger-4 hover:shadow-2xl transition-all duration-300">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h2 class="text-lg md:text-xl font-bold text-white mb-1">Track Transfer</h2>
                                    <p class="text-sm text-white/70">Monitor your transaction status in real-time</p>
                                </div>
                                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center animate-float">
                                    <i class="ri-map-pin-line text-brand-gold text-2xl"></i>
                                </div>
                            </div>
                            <button onclick="openTrackTransferModal()" class="w-full bg-brand-gold hover:bg-brand-gold/90 text-brand-teal font-bold py-3 rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-brand-gold/20 hover:scale-105 transform\">
                                <i class="ri-search-line mr-2"></i>Track Your Transfer
                            </button>
                        </div>
                        <div id="recipients-widget"
                            class="bg-card-white rounded-xl md:rounded-2xl p-4 md:p-6 shadow-sm animate-fade-in-up stagger-2">
                            <div class="flex items-center justify-between mb-3 md:mb-5">
                                <h2 class="text-base md:text-lg font-semibold text-text-primary animate-slide-in-left">Quick Recipients</h2>
                                <a href="{{route('user.reps')}}"
                                    class="text-brand-teal text-xs md:text-sm font-medium hover:underline hover:scale-110 transition-transform">Manage</a>
                            </div>
                            <div class="space-y-2 md:space-y-4">
                                @forelse($rep as $r)
                                <div
                                    class="flex items-center justify-between p-2 md:p-3 rounded-lg md:rounded-xl hover:bg-bg-main transition-all cursor-pointer text-sm">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="font-medium text-text-primary">{{$r->name}}</div>
                                            <div class="text-xs text-text-secondary hidden md:block">Last sent: {{ showAmount($r->last_amount_sent) }}
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('user.send.money.now') }}?recipient={{ $r->id }}"
                                        <button
                                            class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-brand-teal/10 hover:bg-brand-teal text-brand-teal hover:text-white flex items-center justify-center transition-all">
                                            <i class="fa-solid fa-paper-plane text-xs md:text-sm"></i>
                                        </button>
                                    </a>
                                </div>
                                @empty
                                    <p>No recipients yet</p>
                                @endforelse
                            </div>
                            <button
                                class="w-full mt-3 md:mt-4 py-2 md:py-3 rounded-lg md:rounded-xl border-2 border-dashed border-border-gray text-text-secondary hover:border-brand-teal hover:text-brand-teal transition-all font-medium text-xs md:text-sm">
                                <i class="fa-solid fa-plus mr-2"></i>
                            <span onclick="openAddRecipientModal()" class="inline cursor-pointer">Add New Recipient</span>

                            </button>
                        </div>
                         <div id="promo-card"
                            class="bg-gradient-to-br from-brand-teal to-brand-teal/80 rounded-xl md:rounded-2xl p-4 md:p-6 shadow-sm text-white animate-fade-in-up stagger-3 hover:shadow-2xl transition-all duration-300\">
                            <div
                                class="w-10 h-10 md:w-12 md:h-12 rounded-lg md:rounded-xl bg-white/20 flex items-center justify-center mb-3 md:mb-4 animate-float">
                                <i class="fa-solid fa-gift text-lg md:text-2xl"></i>
                            </div>
                            <h3 class="text-base md:text-xl font-semibold mb-2 animate-slide-in-left">Send Money with Zero Fees*</h3>
                            <p class="text-white/90 text-xs md:text-sm mb-4 md:mb-5 leading-relaxed">Limited time offer for new transfers.</p>
                            <a href="{{ route('user.send.money.now') }}">
                                <button
                                class="w-full py-2 md:py-3 bg-brand-gold text-brand-teal rounded-lg md:rounded-xl font-semibold hover:bg-opacity-90 transition-all shadow-lg text-sm md:text-base hover:scale-110 transform hover:shadow-xl hover:animate-bounce-light">Get Started</button></a>
                        </div>
                    </div>
                </div>
    
    <!-- Track Transfer Modal -->
    <div id="trackTransferModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-3 sm:p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 p-4 sm:p-6 flex items-start sm:items-center justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <h2 class="text-xl sm:text-2xl font-bold text-brand-teal">Track Your Transfer</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Enter your transaction ID to check the status</p>
                </div>
                <button onclick="closeTrackTransferModal()" class="text-gray-400 hover:text-gray-600 transition-colors flex-shrink-0">
                    <i class="ri-close-line text-xl sm:text-2xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-4 sm:p-6 space-y-6">
                <!-- Search Section -->
                <div>
                    <label class="block text-sm sm:text-base font-semibold text-brand-teal mb-2 sm:mb-3">Transaction ID</label>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-2">
                        <input id="trackTransactionId" type="text" placeholder="e.g., 001234"
                            class="flex-1 px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-xl focus:outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 transition-all" />
                        <button onclick="searchTransfer()" class="px-4 sm:px-6 py-2.5 sm:py-3 bg-brand-teal text-white text-sm sm:text-base font-semibold rounded-xl hover:bg-brand-teal/90 transition-colors whitespace-nowrap">
                            <i class="ri-search-line mr-1 sm:mr-2"></i><span class="hidden sm:inline">Search</span><span class="sm:hidden">Find</span>
                        </button>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-500 mt-2">Try: 001234, 005678, or 009012</p>
                </div>

                <!-- Recent Transactions Section -->
                <div id="recentTransactionsSection">
                    <h3 class="text-base sm:text-lg font-semibold text-brand-teal mb-3 sm:mb-4">Recent Transfers</h3>
                    @forelse ($transfers as $t)
                    <div id="" class="space-y-2 sm:space-y-3">
                        <div class="p-3 sm:p-4 border border-gray-200 rounded-xl hover:border-brand-teal hover:shadow-md transition-all cursor-pointer"
                            onclick="selectRecentTransaction('{{$t->mtcn_number}}')">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs sm:text-sm text-gray-500 mb-1">Transaction ID: <span class="font-mono font-semibold text-gray-700">{{$t->mtcn_number}}</span></p>
                                    <p class="text-sm sm:text-base font-semibold text-gray-800 truncate">{{ $t->recipient->name }}</p>
                                </div>
                                @php
                                    $badge = $transfer->statusBadge
                                    @endphp
            
                                <span class="inline-flex items-center px-2 md:px-3 py-1 rounded-full text-xs font-medium
                                            {{ $badge['bg'] }} {{ $badge['textColor'] }}">
                                            
                                            <span class="w-1 h-1 rounded-full mr-1 {{ $badge['dot'] }}"></span>
                                            <span class="max-sm:text-[10px]">{{ $badge['text'] }}</span>
                                        </span>
                            </div>
                            <div class="flex items-center justify-between text-xs sm:text-sm text-gray-600">
                                <span class="font-mono text-brand-teal font-semibold">{{ $t->sending_amount }}</span>
                                <span>{{ showDateTime($t->created_at) }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                        <h6>No transactions yet</h6>
                    @endforelse
                </div>

                <!-- Tracking Results -->
                <div id="trackingResults" class="hidden">
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 p-4 sm:p-6">
                <button onclick="closeTrackTransferModal()" class="w-full px-4 sm:px-6 py-2.5 sm:py-3 border border-gray-300 text-gray-700 text-sm sm:text-base font-semibold rounded-xl hover:bg-gray-100 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
    
    <script>
        function selectRecentTransaction(txnId) {
            document.getElementById('trackTransactionId').value = txnId;
            searchTransfer();
        }
        function openTrackTransferModal() {
            document.getElementById('trackTransferModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            document.getElementById('trackTransactionId').focus();
            // displayRecentTransactions();
        }

        function closeTrackTransferModal() {
            document.getElementById('trackTransferModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            document.getElementById('trackTransactionId').value = '';
            document.getElementById('trackingResults').classList.add('hidden');
            // Show recent transactions when closing modal
            document.getElementById('recentTransactionsSection').classList.remove('hidden');
        }
        function searchTransfer() {
            const transactionId = document.getElementById('trackTransactionId').value.trim();
            if (!transactionId) {
                console.warn("No transaction ID entered");
                return;
            }
        
            fetch(`/user/transfers/track/${transactionId}`, {
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
            })
            .then(async res => {
                if (!res.ok) {
                    // Log full response details
                    console.error("Request failed", {
                        status: res.status,
                        statusText: res.statusText
                    });
        
                    // Try to read backend error message
                    const text = await res.text();
                    console.error("Response body:", text);
        
                    throw new Error(`Request failed with status ${res.status}`);
                }
        
                return res.json();
            })
            .then(res => {
                console.log("API response:", res);
        
                if (res.success) {
                    document
                        .getElementById('recentTransactionsSection')
                        .classList.add('hidden');
        
                    displayTrackingResults(res.data);
                } else {
                    console.warn("API returned success=false", res);
                }
            })
            .catch(error => {
                console.error("Search transfer error:", error);
                console.trace();
        
                alert('Transaction ID not found. Please check and try again.');
            });
        }
    function displayTrackingResults(data) {
        const resultsDiv = document.getElementById('trackingResults');
    
        const stepsHtml = data.timeline.map((step, index) => `
            <div class="flex gap-4 pb-4 ${index !== data.timeline.length - 1 ? 'border-b border-gray-200' : ''}">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center
                        ${step.completed ? 'bg-green-100 text-green-600' : 'bg-gray-200 text-gray-400'}">
                        <i class="${step.icon}"></i>
                    </div>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">${step.step}</p>
                    <p class="text-sm text-gray-500">${step.time}</p>
                </div>
            </div>
        `).join('');
    
        resultsDiv.innerHTML = `
            <h3 class="font-semibold text-lg text-brand-teal mb-4">Search Results</h3>
    
            <div class="flex justify-between items-center mb-4">
                <div>
                    <p class="text-sm text-gray-500">Transaction ID</p>
                    <p class="font-mono font-semibold">${data.mtcn}</p>
                </div>
                <span class="px-4 py-2 rounded-full text-sm font-semibold
                    ${data.statusColor}">
                    ${data.status}
                </span>
            </div>
    
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div>
                    <p class="text-xs text-gray-500">Recipient</p>
                    <p class="font-semibold">${data.recipient}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Amount</p>
                    <p class="font-semibold text-brand-teal">
                        ${data.amount}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Date</p>
                    <p class="font-semibold">${data.date}</p>
                </div>
            </div>
    
            <h4 class="font-semibold text-gray-800 mb-3">Transfer Timeline</h4>
            ${stepsHtml}
        `;
    
        resultsDiv.classList.remove('hidden');
    }

    </script>

@if(!empty($pendingReceiptDeposit))
<div id="receiptModal"
     class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-md w-full shadow-2xl overflow-hidden">

        {{-- Header --}}
        <div class="p-6 text-white" style="background-color:#084b5f;">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background:rgba(255,255,255,0.15)">
                    <i class="ri-file-upload-line text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg leading-tight">@lang('Action Required')</h3>
                    <p class="text-sm" style="color:rgba(255,255,255,0.7)">@lang('Upload your Interac e-Transfer receipt')</p>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-6">
            <p class="text-sm text-slate-600 dark:text-slate-300 mb-5">
                @lang('Your Interac e-Transfer of')
                <strong>{{ showAmount($pendingReceiptDeposit->final_amount, currencyFormat: false) }} {{ $pendingReceiptDeposit->method_currency }}</strong>
                @lang('on') {{ showDateTime($pendingReceiptDeposit->created_at) }}
                @lang('has been confirmed. Please attach your bank receipt to complete this transaction.')
            </p>

            <form action="{{ route('user.deposit.receipt.upload', $pendingReceiptDeposit->id) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                    @lang('Bank Receipt') <span class="text-red-500">*</span>
                </label>

                <input type="file"
                       name="receipt"
                       accept=".jpg,.jpeg,.png,.pdf"
                       required
                       class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-3 py-2 text-sm mb-1
                              file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0
                              file:text-sm file:font-medium
                              file:bg-slate-100 dark:file:bg-slate-700
                              file:text-slate-700 dark:file:text-slate-200">

                @error('receipt')
                    <p class="text-red-500 text-xs mt-1 mb-3">{{ $message }}</p>
                @else
                    <p class="text-xs text-slate-400 mt-1 mb-5">@lang('Accepted: JPG, PNG, PDF — max 5 MB')</p>
                @enderror

                <button type="submit"
                        class="w-full font-semibold py-3 rounded-2xl text-white text-sm transition-opacity hover:opacity-90"
                        style="background-color:#084b5f;">
                    <i class="ri-upload-cloud-line mr-1"></i> @lang('Upload Receipt')
                </button>
            </form>
        </div>
    </div>
</div>
<script>document.body.style.overflow = 'hidden';</script>
@endif

@endsection