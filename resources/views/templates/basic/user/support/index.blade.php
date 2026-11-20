@extends($activeTemplate . 'layouts.master')
@section('activePage', 'support')
@section('content')
<!--<section class="dashboard-section body-collapse">-->
<!--    <div class="overlay pt-120">-->
<!--        <div class="container-fruid">-->
<!--            <div class="d-flex flex-wrap flex-row-reverse justify-content-between align-items-center mb-lg-3">-->
<!--                <div class="text-end">-->
<!--                    <a href="{{ route('ticket.open') }}" class="btn btn-sm btn--base mb-2"> <i class="la la-plus"></i>-->
<!--                        @lang('New Ticket')</a>-->
<!--                </div>-->
<!--                <div class="custom--table__header">-->
<!--                    <h5 class="text-lg-start m-0 text-center">@lang('Support Ticket')</h5>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="table-responsive--md">-->
<!--                <table class="table custom--table">-->
<!--                    <thead>-->
<!--                        <tr>-->
<!--                            <th>@lang('Subject')</th>-->
<!--                            <th>@lang('Status')</th>-->
<!--                            <th>@lang('Priority')</th>-->
<!--                            <th>@lang('Last Reply')</th>-->
<!--                            <th>@lang('Action')</th>-->
<!--                        </tr>-->
<!--                    </thead>-->
<!--                    <tbody>-->
<!--                        @forelse ($supports as $support)-->
<!--                            <tr>-->
<!--                                <td>[@lang('Ticket')#{{ $support->ticket }}] {{ __($support->subject) }}</td>-->
<!--                                <td>@php echo $support->statusBadge; @endphp</td>-->
<!--                                <td>-->
<!--                                    @if ($support->priority == Status::PRIORITY_LOW)-->
<!--                                        <span class="badge badge--dark">@lang('Low')</span>-->
<!--                                    @elseif($support->priority == Status::PRIORITY_MEDIUM)-->
<!--                                        <span class="badge badge--primary">@lang('Medium')</span>-->
<!--                                    @elseif($support->priority == Status::PRIORITY_HIGH)-->
<!--                                        <span class="badge badge--warning">@lang('High')</span>-->
<!--                                    @endif-->
<!--                                </td>-->
<!--                                <td>{{ diffForHumans($support->last_reply) }} </td>-->
<!--                                <td>-->
<!--                                    <a href="{{ route('ticket.view', $support->ticket) }}" class="btn btn--base btn--sm">-->
<!--                                        <i class="las la-desktop"></i>-->
<!--                                    </a>-->
<!--                                </td>-->
<!--                            </tr>-->
<!--                        @empty-->
<!--                            <tr>-->
<!--                                <td colspan="100%" class="text-center text-muted">{{ __($emptyMessage) }} </td>-->
<!--                            </tr>-->
<!--                        @endforelse-->
<!--                    </tbody>-->
<!--                </table>-->
<!--            </div>-->
<!--            @if ($supports->hasPages())-->
<!--                <div class="mt-4 paginate">-->
<!--                    {{ paginateLinks($supports) }}-->
<!--                </div>-->
<!--            @endif-->
<!--        </div>-->
<!--    </div>-->
<!--</section>-->

<div class="max-w-6xl mx-auto p-4 md:p-8">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
                        <div>
                            <h2 class="text-2xl font-bold text-text-primary tracking-tight">Support Concierge</h2>
                            <p class="text-text-secondary text-sm mt-1">View and manage your private banking inquiries.</p>
                        </div>
                        <button id="openTicketBtn"
                            class="flex items-center justify-center gap-2 bg-brand-gold text-brand-teal px-6 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-brand-gold/20 hover:scale-[1.02] transition-all">
                            <i class="ri-add-line text-lg"></i> Open New Ticket
                        </button>
                    </div>

                    <div class="bg-card-white rounded-[2.5rem] shadow-sm border border-border-gray overflow-hidden">
                        <div class="p-8 border-b border-border-light bg-bg-secondary/30 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-brand-teal uppercase tracking-widest">Recent Tickets</h3>
                            <!--<span id="ticketCount"-->
                            <!--    class="text-[10px] font-bold text-text-secondary bg-card-white px-3 py-1 rounded-full border border-border-gray">Total:-->
                            <!--    2</span>-->
                        </div>
                        @if(empty($supports))
                        <!-- Empty State -->
                            <div id="emptyTicketsState"
                                class="hidden py-24 flex flex-col items-center justify-center text-center px-6">
                                <div
                                    class="w-20 h-20 bg-bg-secondary rounded-full flex items-center justify-center mb-6 text-text-secondary">
                                    <i class="ri-customer-service-2-line text-4xl"></i>
                                </div>
                                <h4 class="text-lg font-bold text-text-primary">No Active Inquiries</h4>
                                <p class="text-text-secondary text-sm max-w-xs mt-2 leading-relaxed">
                                    Your communication log is currently empty. Our support team is available 24/7 for your
                                    banking needs.
                                </p>
                            </div>
                        @else

                            <!-- Tickets List -->
                            <div id="ticketsList">
                                @foreach ($supports as $support)
                                <div
                                    class="border-b border-border-light hover:bg-bg-secondary/50 transition-colors cursor-pointer group">
                                    <a href="{{ route('ticket.view', $support->ticket) }}">
                                        <div class="p-6 md:p-8">
                                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center">
                                                        <i class="ri-check-double-line text-emerald-600 text-lg"></i>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-bold text-text-primary">@lang('Ticket')#{{ $support->ticket }}</h4>
                                                        <p class="text-sm text-text-secondary mt-1">{{ __($support->subject) }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    
                                                    <span
                                                        class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold uppercase tracking-widest border border-emerald-200">@php echo $support->statusBadge; @endphp</span>
                                                        
                                                    <i
                                                        class="ri-arrow-right-s-line text-text-secondary group-hover:text-text-primary transition-colors"></i>
                                                </div>
                                            </div>
                                            <div
                                                class="flex flex-col md:flex-row md:items-center justify-between gap-2 ml-16 text-xs text-text-secondary">
                                                <span>Priority: 
                                                    <span class="font-bold text-text-primary">
                                                        @if ($support->priority == Status::PRIORITY_LOW)
                                                            @lang('Low')
                                                        @elseif($support->priority == Status::PRIORITY_MEDIUM)
                                                            @lang('Medium')
                                                        @elseif($support->priority == Status::PRIORITY_HIGH)
                                                            @lang('High')
                                                        @endif
                                                    </span>
                                                </span>
                                                <span class="hidden md:inline">•</span>
                                                <span>Created: <span class="font-bold text-text-primary">{{ $support->created_at->format('dS F, Y') }}</span></span>
                                                <span class="hidden md:inline">•</span>
                                                <span>Response: <span class="font-bold text-emerald-600">{{ diffForHumans($support->last_reply) }}</span></span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <div id="ticketFormContainer" class="hidden mx-auto p-4 md:p-8 animate-fade-in">
                    <div class="bg-card-white rounded-[2.5rem] p-8 md:p-12 shadow-sm border border-border-gray">
                        <div class="mb-10 flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-brand-teal rounded-2xl flex items-center justify-center text-white shadow-lg shadow-brand-teal/20">
                                <i class="ri-mail-send-line text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-text-primary tracking-tight">Submit Inquiry</h3>
                        </div>

                        <form id="supportForm" class="space-y-8" action="{{ route('ticket.store') }}" class="disableSubmission" method="post"
                        enctype="multipart/form-data">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <input type="hidden" name="name" value="{{ @$user->firstname . ' ' . @$user->lastname }}">
                                <input type="hidden" name="email" value="{{ @$user->email }}">
                                <div class="md:col-span-2 space-y-3">
                                    <label
                                        class="text-[11px] uppercase tracking-widest text-text-secondary font-bold ml-1">Subject</label>
                                    <input type="text" placeholder="Brief summary of your request" name="subject" value="{{ old('subject') }}"
                                        class="w-full px-5 py-4 bg-bg-secondary border border-border-gray rounded-2xl text-sm focus:bg-card-white focus:border-brand-teal outline-none transition-all">
                                </div>

                                <div class="space-y-3">
                                    <label
                                        class="text-[11px] uppercase tracking-widest text-text-secondary font-bold ml-1">Priority
                                        Level</label>
                                    <select name="priority"
                                        class="w-full px-5 py-4 bg-bg-secondary border border-border-gray rounded-2xl text-sm text-brand-teal font-bold outline-none appearance-none cursor-pointer focus:bg-card-white">
                                        <option value="3">High</option>
                                        <option value="2">Medium</option>
                                        <option value="1">Low</option>
                                    </select>
                                </div>

                                <div class="md:col-span-3 space-y-3">
                                    <label
                                        class="text-[11px] uppercase tracking-widest text-text-secondary font-bold ml-1">Detailed
                                        Message</label>
                                    <textarea name="message" rows="6" placeholder="Describe your inquiry in detail..."
                                        class="w-full px-5 py-4 bg-bg-secondary border border-border-gray rounded-2xl text-sm focus:bg-card-white focus:border-brand-teal outline-none transition-all resize-none">{{ old('message') }}</textarea>
                                </div>
                            </div>

                            <div
                                class="p-6 bg-bg-secondary rounded-[2rem] border-2 border-dashed border-border-gray group hover:border-brand-teal/30 transition-all">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <i
                                        class="ri-attachment-2 text-2xl text-text-secondary group-hover:text-brand-teal transition-colors mb-2"></i>
                                    <p class="text-xs font-bold text-text-primary">Add Supporting Documents</p>
                                    <p class="text-[10px] text-text-secondary mt-1">Max 5 files | PNG, JPG, PDF (Max 1GB
                                        total)</p>
                                    <input type="file" name="attachments[]" multiple class="hidden" id="fileUpload">
                                    <div id="filePreview" class="mt-4 space-y-2 text-xs text-text-primary"></div>
                                    <button type="button" onclick="document.getElementById('fileUpload').click()"
                                        class="mt-4 px-6 py-2 bg-card-white border border-border-gray rounded-xl text-[10px] font-black uppercase tracking-widest hover:shadow-md transition-all">
                                        Browse Files
                                    </button>
                                </div>
                            </div>

                            <div
                                class="pt-6 border-t border-border-light flex flex-col md:flex-row items-center justify-between gap-6">
                                <div class="flex items-center gap-2 text-brand-teal">
                                    <i class="ri-shield-user-line text-lg"></i>
                                    <span class="text-[10px] font-bold uppercase tracking-widest">Secure Client
                                        Communication</span>
                                </div>
                                <button type="submit"
                                    class="w-full md:w-auto bg-brand-teal text-white px-12 py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-brand-teal/20 hover:bg-brand-teal/90 transition-all flex items-center justify-center gap-3">
                                    Send Ticket <i class="ri-send-plane-fill"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            
            <script>
        // Toggle ticket form visibility
        document.getElementById('openTicketBtn').addEventListener('click', function() {
            const formContainer = document.getElementById('ticketFormContainer');
            if (formContainer.classList.contains('hidden')) {
                formContainer.classList.remove('hidden');
                formContainer.classList.add('animate-fade-in');
                // Scroll to form smoothly
                setTimeout(() => {
                    formContainer.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 100);
            }
        });

        window.addEventListener('DOMContentLoaded', function() {
            // Initialize sidebar after page load
            window.initSidebar();

            // Initialize sidebar toggle button
            const toggleBtn = document.getElementById('toggle-sidebar');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    window.toggleSidebar();
                });
            }
        });
    </script>
    
    <script>
    const fileInput = document.getElementById('fileUpload');
    const preview = document.getElementById('filePreview');

    fileInput.addEventListener('change', function () {
        preview.innerHTML = '';

        if (!this.files.length) return;

        Array.from(this.files).forEach((file, index) => {
            const fileRow = document.createElement('div');
            fileRow.className = 'flex items-center justify-between bg-card-white border border-border-gray rounded-xl px-4 py-2';

            fileRow.innerHTML = `
                <span class="truncate">${file.name}</span>
                <span class="text-[10px] text-text-secondary">
                    ${(file.size / 1024 / 1024).toFixed(2)} MB
                </span>
            `;

            preview.appendChild(fileRow);
        });
    });
</script>

            
@endsection

@push('style')
    <style>
        .paginate p {
            margin: 0px !important;
        }
    </style>
@endpush
