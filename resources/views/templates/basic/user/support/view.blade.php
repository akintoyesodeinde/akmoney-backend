@extends($activeTemplate . 'layouts.' . $layout)
@section('content')
<!--<section class="dashboard-section body-collapse">-->
<!--    <div class="overlay pt-120">-->
<!--        <div class="container-fruid">-->
<!--            <div class="row justify-content-center">-->
<!--                <div class="col-md-12">-->
<!--                    <div class="card custom--card">-->
<!--                        <div class="card-header card-header-bg d-flex flex-wrap justify-content-between align-items-center">-->
<!--                            <h5 class="m-0">-->
<!--                                @php echo $myTicket->statusBadge; @endphp-->
<!--                                [@lang('Ticket')#{{ $myTicket->ticket }}] {{ $myTicket->subject }}-->
<!--                            </h5>-->
<!--                            @if ($myTicket->status != Status::TICKET_CLOSE && $myTicket->user)-->
<!--                                <button class="btn btn-danger close-button btn-sm closeButton" data-bs-toggle="modal"-->
<!--                                    data-bs-target="#closeModal" type="button"><i class="fa fa-lg fa-times-circle"></i>-->
<!--                                </button>-->
<!--                            @endif-->
<!--                        </div>-->
<!--                        <div class="card-body">-->
<!--                            <form method="post" action="{{ route('ticket.reply', $myTicket->id) }}"-->
<!--                                enctype="multipart/form-data" class="disableSubmission">-->
<!--                                @csrf-->
<!--                                <div class="row justify-content-between">-->
<!--                                    <div class="col-md-12">-->
<!--                                        <div class="form-group">-->
<!--                                            <textarea name="message" class="form-control form--control-textarea" rows="4">{{ old('message') }}</textarea>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="row mt-4">-->
<!--                                    <div class="col-md-9">-->
<!--                                        <button type="button" class="btn btn-dark btn-sm addAttachment my-2">-->
<!--                                            <i class="las la-plus"></i>-->
<!--                                            @lang('Add Attachment')-->
<!--                                        </button>-->
<!--                                        <p class="mb-2"><span class="text--info">@lang('Max 5 files can be uploaded | Maximum upload size is ' . convertToReadableSize(ini_get('upload_max_filesize')) . ' | Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')</span></p>-->
<!--                                        <div class="row fileUploadsContainer gy-3">-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                    <div class="col-md-3">-->
<!--                                        <button class="btn btn--base w-100 my-2" type="submit">-->
<!--                                            <i class="la la-fw la-lg la-reply"></i>-->
<!--                                            @lang('Reply')-->
<!--                                        </button>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </form>-->
<!--                        </div>-->
<!--                    </div>-->

<!--                    <div class="card custom--card mt-4">-->
<!--                        <div class="card-body">-->
<!--                            @foreach ($messages as $message)-->
<!--                                @if ($message->admin_id == 0)-->
<!--                                    <div class="row border border-primary border-radius-3 my-3 py-3 mx-2">-->
<!--                                        <div class="col-md-3 border-end text-md-end">-->
<!--                                            <h5 class="my-3">{{ $message->ticket->name }}</h5>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-9">-->
<!--                                            <p class="text-muted fw-bold my-3">-->
<!--                                                @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}</p>-->
<!--                                            <p>{{ $message->message }}</p>-->
<!--                                            @if ($message->attachments->count() > 0)-->
<!--                                                <ul class="list list--row">-->
<!--                                                    @foreach ($message->attachments as $k => $image)-->
<!--                                                        <li>-->
<!--                                                            <a href="{{ route('ticket.download', encrypt($image->id)) }}"-->
<!--                                                                class="t-link file-button">-->
<!--                                                                <span class="d-inline-block me-1">-->
<!--                                                                    <i class="fa fa-file"></i>-->
<!--                                                                </span> @lang('Attachment') {{ ++$k }}-->
<!--                                                            </a>-->
<!--                                                        </li>-->
<!--                                                    @endforeach-->
<!--                                                </ul>-->
<!--                                            @endif-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                @else-->
<!--                                    <div class="row border border-warning border-radius-3 my-3 py-3 mx-2"-->
<!--                                        style="background-color: #ffd96729">-->
<!--                                        <div class="col-md-3 border-end text-md-end">-->
<!--                                            <h5 class="my-3">{{ $message->admin->name }}</h5>-->
<!--                                            <p class="lead text-muted">@lang('Staff')</p>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-9">-->
<!--                                            <p class="text-muted fw-bold my-3">-->
<!--                                                @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}-->
<!--                                            </p>-->
<!--                                            <p>{{ $message->message }}</p>-->
<!--                                            @if ($message->attachments->count() > 0)-->
<!--                                                <div class="mt-2">-->
<!--                                                    @foreach ($message->attachments as $k => $image)-->
<!--                                                        <a href="{{ route('ticket.download', encrypt($image->id)) }}"-->
<!--                                                            class="t-link file-button">-->
<!--                                                            <span class="d-inline-block me-1">-->
<!--                                                                <i class="fa fa-file"></i>-->
<!--                                                            </span> @lang('Attachment') {{ ++$k }}-->
<!--                                                        </a>-->
<!--                                                    @endforeach-->
<!--                                                </div>-->
<!--                                            @endif-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                @endif-->
<!--                            @endforeach-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</section>-->

    <!--<div id="closeModal" class="modal custom--modal fade">-->
    <!--    <div class="modal-dialog" role="document">-->
    <!--        <div class="modal-content">-->
    <!--            <form action="{{ route('ticket.close', $myTicket->id) }}" method="POST" class="disableSubmission">-->
    <!--                @csrf-->
    <!--                <div class="modal-header">-->
    <!--                    <h5 class="modal-title">@lang('Confirmation Alert!')</h5>-->
    <!--                    <button type="button" class="close btn btn--danger btn-sm close-button" data-bs-dismiss="modal"-->
    <!--                        aria-label="Close">-->
    <!--                        <i class="la la-times" aria-hidden="true"></i>-->
    <!--                    </button>-->
    <!--                </div>-->
    <!--                <div class="modal-body">-->
    <!--                    @lang('Are you sure to close this ticket?')-->
    <!--                </div>-->
    <!--                <div class="modal-footer">-->
    <!--                    <button type="button" class="btn btn--dark btn-sm" data-bs-dismiss="modal">@lang('No')</button>-->
    <!--                    <button type="submit" class="btn btn--primary btn-sm">@lang('Yes')</button>-->
    <!--                </div>-->
    <!--            </form>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
    <div class="max-w-5xl mx-auto">
        <!-- Back Button -->
        <a href="{{route('ticket.index')}}" class="inline-flex items-center gap-2 text-text-secondary hover:text-brand-teal transition-all duration-300 mb-6 text-sm font-bold group">
            <i class="ri-arrow-left-line group-hover:-translate-x-1 transition-transform duration-300"></i>
            <span>Back to Support</span>
        </a>

        <!-- Ticket Card -->
        <div class="bg-card-white rounded-[2.5rem] shadow-sm border border-border-gray overflow-hidden mb-8 animate-fade-in">
            <!-- Ticket Header -->
            <div class="px-6 md:px-8 py-5 md:py-6 border-b border-border-light bg-bg-secondary/30">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-100 to-cyan-100 flex items-center justify-center shadow-lg shadow-blue-100 animate-pulse-slow">
                            <i class="ri-mail-line text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-text-primary tracking-tight">@lang('Ticket')#{{ $myTicket->ticket }}</h2>
                            <p class="text-sm text-text-secondary">{{ $myTicket->subject }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-4 py-2 bg-blue-50 text-blue-700 rounded-full text-xs font-bold uppercase tracking-widest border border-blue-200 animate-pulse">@php echo $myTicket->statusBadge; @endphp</span>
                    </div>
                </div>
            </div>

            <!-- Messages Thread -->
            <div class="p-6 md:p-8">
                <div class="space-y-6">
                    <!-- User Message -->
                    @foreach ($messages as $message)
                        @if ($message->admin_id == 0)
                        @php
                            $words = explode(' ', trim($message->ticket->name));
                            $initials = strtoupper(
                                substr($words[0], 0, 1) .
                                (isset($words[1]) ? substr($words[1], 0, 1) : '')
                            );
                        @endphp
                            <div class="flex gap-4 animate-slide-in-left">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-brand-teal to-teal-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0 shadow-lg shadow-brand-teal/30">
                                    {{$initials}}
                                </div>
                                <div class="flex-1">
                                    <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-2 mb-2">
                                        <span class="font-bold text-text-primary text-sm">{{ $message->ticket->name }}</span>
                                        <span class="text-[10px] text-text-secondary flex items-center gap-1">
                                            <i class="ri-time-line"></i>@lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}
                                        </span>
                                    </div>
                                    <div class="bg-bg-secondary p-5 rounded-2xl rounded-tl-none shadow-sm hover:shadow-md transition-all duration-300 space-y-4">
                                        <p class="text-text-primary text-sm leading-relaxed">{{ $message->message }}</p>
                                        {{-- Attachments --}}
                                        @if ($message->attachments->count() > 0)
                                            <div class="flex flex-wrap gap-3 pt-3 border-t border-gray-200">
                                                @foreach ($message->attachments as $k => $file)
                                                    <a href="{{ route('ticket.download', encrypt($file->id)) }}"
                                                       class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg
                                                              border-border-light bg-bg-secondary/30 hover:border-brand-teal
                                                              hover:bg-brand-teal/5 transition-all">
                            
                                                        <i class="ri-attachment-2 text-brand-teal"></i>
                                                        <span>@lang('Attachment') {{ $k + 1 }}</span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            @php
                                $words = explode(' ', trim($message->admin->name));
                                $initials = strtoupper(
                                    substr($words[0], 0, 1) .
                                    (isset($words[1]) ? substr($words[1], 0, 1) : '')
                                );
                            @endphp
                                <div class="flex gap-4 animate-slide-in-left">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-brand-teal to-teal-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0 shadow-lg shadow-brand-teal/30">
                                        {{$initials}}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-2 mb-2">
                                            <span class="font-bold text-text-primary text-sm">{{ $message->admin->name }}</span>
                                            <span class="text-[10px] text-text-secondary flex items-center gap-1">
                                                <i class="ri-time-line"></i>@lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}
                                            </span>
                                        </div>
                                        <div class="bg-bg-secondary p-5 rounded-2xl rounded-tl-none shadow-sm hover:shadow-md transition-all duration-300 space-y-4">
                                            <p class="text-text-primary text-sm leading-relaxed">{{ $message->message }}</p>
                                            {{-- Attachments --}}
                                            @if ($message->attachments->count() > 0)
                                                <div class="flex flex-wrap gap-3 pt-3 border-t border-gray-200">
                                                    @foreach ($message->attachments as $k => $file)
                                                        <a href="{{ route('ticket.download', encrypt($file->id)) }}"
                                                           class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg
                                                                  border-border-light bg-bg-secondary/30 hover:border-brand-teal
                                                                  hover:bg-brand-teal/5 transition-all">
                                
                                                            <i class="ri-attachment-2 text-brand-teal"></i>
                                                            <span>@lang('Attachment') {{ $k + 1 }}</span>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                        @endif
                    @endforeach

                    <!-- Typing Indicator (Demo) -->
                    <div id="typingIndicator" class="hidden flex gap-4 animate-slide-in-left">
                        <div class="w-10 h-10 rounded-full bg-bg-secondary flex items-center justify-center text-text-secondary text-sm font-bold flex-shrink-0">
                            <i class="ri-user-line"></i>
                        </div>
                        <div class="flex items-center gap-1 bg-bg-secondary px-4 py-3 rounded-2xl rounded-tl-none">
                            <span class="w-2 h-2 bg-text-secondary rounded-full animate-bounce"></span>
                            <span class="w-2 h-2 bg-text-secondary rounded-full animate-bounce" style="animation-delay: 0.1s"></span>
                            <span class="w-2 h-2 bg-text-secondary rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reply Section -->
            <div class="px-6 md:px-8 py-6 border-t border-border-light bg-bg-secondary/30">
                <!-- Reply Input -->
                @if($myTicket->status != 3)
                <div id="replySection" class="transition-all duration-300">
                    <form method="post" action="{{ route('ticket.reply', $myTicket->id)}}" enctype="multipart/form-data" class="disableSubmission">
                        
                        @csrf
                        <div class="flex flex-col gap-3 mb-4">
                            <textarea id="replyInput" rows="3" name="message" placeholder="Type your reply..." 
                                class="w-full px-5 py-4 bg-card-white border border-border-gray rounded-2xl text-sm focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20 outline-none transition-all resize-none"></textarea>
                        </div>
                        <!-- Attachments Section -->
                        <div class="bg-bg-secondary/50 mb-4 hidden" id="attachmentSec">
                            <p class="text-[11px] uppercase tracking-widest text-text-secondary font-bold mb-3 flex items-center gap-2">
                                <i class="ri-attachment-line"></i> Add Attachment
                            </p>
                            <div id="fileUploadArea" class="p-4 bg-card-white rounded-2xl border-2 border-dashed border-border-gray cursor-pointer transition-all duration-300 hover:border-brand-teal/50 hover:bg-bg-secondary/30 group">
                                <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-bg-secondary rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <i class="ri-attachment-2 text-xl text-text-secondary group-hover:text-brand-teal transition-colors"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-text-primary group-hover:text-brand-teal transition-colors">Max 5 files can be uploaded</p>
                                            <p class="text-[10px] text-text-secondary">Maximum upload size is 1024MB</p>
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-text-secondary bg-bg-secondary px-3 py-1 rounded-full">.jpg, .jpeg, .png, .pdf, .doc, .docx</p>
                                </div>
                                <input type="file" name="attachments[]" id="fileInput" multiple class="hidden" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            </div>
                            <!-- Uploaded Files Preview -->
                            <div id="uploadedFiles" class="mt-4 space-y-2 hidden"></div>
                        </div>
                        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                            <button id="attachBtn" type="button" onclick="showAttachment()" class="flex items-center justify-center gap-2 px-4 py-3 bg-card-white border border-border-gray rounded-xl text-xs font-bold uppercase tracking-widest hover:shadow-md hover:border-brand-teal/30 transition-all text-text-secondary hover:text-brand-teal">
                                <i class="ri-attachment-2"></i> Attach
                            </button>
                            <button id="attachClose" type="button" onclick="closeAttachment()" class="hidden flex items-center justify-center gap-2 px-4 py-3 bg-card-white border border-border-gray rounded-xl text-xs font-bold uppercase tracking-widest hover:shadow-md hover:border-brand-teal/30 transition-all text-text-secondary hover:text-brand-teal">
                                <i class="ri-attachment-2"></i> Close
                            </button>
                            <button type="submit" id="sendReplyBtn" class="flex items-center justify-center gap-2 px-8 py-4 bg-brand-teal text-white rounded-2xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-brand-teal/20 hover:bg-brand-teal/90 hover:shadow-xl hover:scale-[1.02] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="ri-send-plane-fill"></i>
                                <span>Send Reply</span>
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                <!-- Close Ticket Alert -->
                <div id="closeTicketAlert" class="hidden p-5 bg-orange-50 border border-orange-200 rounded-2xl animate-fade-in">
                    <form action="{{ route('ticket.close', $myTicket->id) }}" method="POST" class="">
                    @csrf
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                <i class="ri-error-warning-line text-orange-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="font-bold text-orange-700 text-sm">Confirmation Alert!</p>
                                <p class="text-xs text-orange-600">Are you sure to close this ticket?</p>
                            </div>
                        </div>
                        <div class="flex gap-3 ml-13">
                            <button type="button" onclick="cancelCloseTicket()" class="flex-1 md:flex-none px-6 py-3 bg-card-white border border-border-gray rounded-xl text-xs font-bold uppercase tracking-widest hover:shadow-md hover:scale-[1.02] transition-all text-text-primary">
                                No
                            </button>
                            <button type="submit" class="flex-1 md:flex-none px-6 py-3 bg-brand-teal text-white rounded-xl text-xs font-bold uppercase tracking-widest shadow-lg shadow-brand-teal/20 hover:bg-brand-teal/90 hover:scale-[1.02] transition-all">
                                Yes
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Close Ticket Button -->
                @if($myTicket->status != 3)
                <button id="closeTicketBtn" onclick="showCloseAlert()" class="mt-4 px-6 py-3 bg-card-white border border-red-200 text-red-600 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-red-50 hover:border-red-300 hover:shadow-md transition-all flex items-center justify-center gap-2">
                    <i class="ri-close-circle-line"></i>
                    <span>Close Ticket</span>
                </button>
                @endif
            </div>
        </div>

        <!-- Ticket Info Cards -->
        <div class="grid grid-cols-3 md:grid-cols-3 gap-4 animate-fade-in" style="animation-delay: 0.2s;">
            <div class="bg-card-white p-4 rounded-2xl border border-border-gray hover:shadow-lg hover:scale-[1.02] transition-all duration-300 cursor-pointer">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="ri-calendar-line text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-text-secondary uppercase tracking-widest">Created</p>
                        <p class="font-bold text-text-primary text-sm">{{ $myTicket->created_at->format('dS F, Y ') }}</p>
                    </div>
                </div>
            </div>
            <!--<div class="bg-card-white p-4 rounded-2xl border border-border-gray hover:shadow-lg hover:scale-[1.02] transition-all duration-300 cursor-pointer">-->
            <!--    <div class="flex items-center gap-3">-->
            <!--        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">-->
            <!--            <i class="ri-reply-line text-emerald-600"></i>-->
            <!--        </div>-->
            <!--        <div>-->
            <!--            <p class="text-[10px] text-text-secondary uppercase tracking-widest">Replies</p>-->
            <!--            <p class="font-bold text-text-primary text-sm">1</p>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            <div class="bg-card-white p-4 rounded-2xl border border-border-gray hover:shadow-lg hover:scale-[1.02] transition-all duration-300 cursor-pointer">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="ri-time-line text-orange-600"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-text-secondary uppercase tracking-widest">Wait Time</p>
                        <p class="font-bold text-text-primary text-sm">{{ diffForHumans($myTicket->last_reply) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-card-white p-4 rounded-2xl border border-border-gray hover:shadow-lg hover:scale-[1.02] transition-all duration-300 cursor-pointer">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="ri-flag-line text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-text-secondary uppercase tracking-widest">Priority</p>
                        <p class="font-bold text-text-primary text-sm">@if ($myTicket->priority == Status::PRIORITY_LOW)
                                                        @lang('Low')
                                                    @elseif($myTicket->priority == Status::PRIORITY_MEDIUM)
                                                        @lang('Medium')
                                                    @elseif($myTicket->priority == Status::PRIORITY_HIGH)
                                                        @lang('High')
                                                    @endif</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="toast" class="fixed bottom-6 right-6 bg-card-white px-6 py-4 rounded-2xl shadow-2xl border border-border-gray flex items-center gap-3 transform translate-y-20 opacity-0 transition-all duration-500 z-50">
        <div id="toastIcon" class="w-8 h-8 rounded-full flex items-center justify-center">
            <i class="ri-checkbox-circle-fill text-lg"></i>
        </div>
        <p id="toastMessage" class="font-bold text-sm text-text-primary"></p>
    </div>
    <script>
        // File Upload Handling
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileInput = document.getElementById('fileInput');
        const uploadedFiles = document.getElementById('uploadedFiles');
        let files = [];

        fileUploadArea.addEventListener('click', () => fileInput.click());

        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('border-brand-teal', 'bg-bg-secondary/30');
        });

        fileUploadArea.addEventListener('dragleave', () => {
            fileUploadArea.classList.remove('border-brand-teal', 'bg-bg-secondary/30');
        });

        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('border-brand-teal', 'bg-bg-secondary/30');
            handleFiles(e.dataTransfer.files);
        });

        fileInput.addEventListener('change', () => {
            handleFiles(fileInput.files);
        });

        function handleFiles(newFiles) {
            const remainingSlots = 5 - files.length;
            const filesToAdd = Array.from(newFiles).slice(0, remainingSlots);
            
            filesToAdd.forEach(file => {
                if (files.length < 5) {
                    files.push(file);
                }
            });

            updateFilePreview();
        }

        function updateFilePreview() {
            if (files.length > 0) {
                uploadedFiles.classList.remove('hidden');
                uploadedFiles.innerHTML = files.map((file, index) => `
                    <div class="flex items-center justify-between p-3 bg-card-white rounded-xl border border-border-gray animate-fade-in">
                        <div class="flex items-center gap-3">
                            <i class="ri-file-line text-xl text-brand-teal"></i>
                            <span class="text-sm font-bold text-text-primary truncate max-w-[200px]">${file.name}</span>
                            <span class="text-[10px] text-text-secondary">${formatFileSize(file.size)}</span>
                        </div>
                        <button onclick="removeFile(${index})" class="text-text-secondary hover:text-red-500 transition-colors p-1">
                            <i class="ri-close-line text-lg"></i>
                        </button>
                    </div>
                `).join('');
            } else {
                uploadedFiles.classList.add('hidden');
            }
        }

        function removeFile(index) {
            files.splice(index, 1);
            updateFilePreview();
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Reply Functionality
        const replyInput = document.getElementById('replyInput');
        const sendReplyBtn = document.getElementById('sendReplyBtn');

        replyInput.addEventListener('input', () => {
            sendReplyBtn.disabled = replyInput.value.trim() === '';
        });

        // sendReplyBtn.addEventListener('click', async () => {
        //     const message = replyInput.value.trim();
        //     if (!message) return;

        //     // Show typing indicator briefly
        //     showTypingIndicator();
            
        //     // Disable button during send
        //     sendReplyBtn.disabled = true;
        //     sendReplyBtn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Sending...';

        //     // Simulate sending (replace with actual API call)
        //     await new Promise(resolve => setTimeout(resolve, 1500));

        //     // Add message to thread
        //     addMessageToThread(message);
            
        //     // Reset
        //     replyInput.value = '';
        //     sendReplyBtn.disabled = true;
        //     sendReplyBtn.innerHTML = '<i class="ri-send-plane-fill"></i><span>Send Reply</span>';
            
        //     showToast('Message sent successfully!', 'success');
        // });


        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function showTypingIndicator() {
            const typingIndicator = document.getElementById('typingIndicator');
            typingIndicator.classList.remove('hidden');
            
            setTimeout(() => {
                typingIndicator.classList.add('hidden');
            }, 2000);
        }

        // Close Ticket Functions
        function showCloseAlert() {
            document.getElementById('replySection').classList.add('hidden');
            document.getElementById('closeTicketBtn').classList.add('hidden');
            document.getElementById('closeTicketAlert').classList.remove('hidden');
        }

        function cancelCloseTicket() {
            document.getElementById('closeTicketAlert').classList.add('hidden');
            document.getElementById('replySection').classList.remove('hidden');
            document.getElementById('closeTicketBtn').classList.remove('hidden');
        }
        
        function showAttachment()
        {
            document.getElementById('attachmentSec').classList.remove('hidden');
            document.getElementById('attachClose').classList.remove('hidden');
            document.getElementById('attachBtn').classList.add('hidden');
        }
        
        function closeAttachment()
        {
            document.getElementById('attachmentSec').classList.add('hidden');
            document.getElementById('attachClose').classList.add('hidden');
            document.getElementById('attachBtn').classList.remove('hidden');
        }

        // Toast Notification
        // function showToast(message, type = 'success') {
        //     const toast = document.getElementById('toast');
        //     const toastIcon = document.getElementById('toastIcon');
        //     const toastMessage = document.getElementById('toastMessage');
            
        //     toastMessage.textContent = message;
            
        //     if (type === 'success') {
        //         toastIcon.className = 'w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center';
        //         toastIcon.innerHTML = '<i class="ri-checkbox-circle-fill text-emerald-600 text-lg"></i>';
        //     } else if (type === 'error') {
        //         toastIcon.className = 'w-8 h-8 rounded-full bg-red-100 flex items-center justify-center';
        //         toastIcon.innerHTML = '<i class="ri-error-warning-fill text-red-600 text-lg"></i>';
        //     }
            
        //     toast.classList.remove('translate-y-20', 'opacity-0');
            
        //     setTimeout(() => {
        //         toast.classList.add('translate-y-20', 'opacity-0');
        //     }, 3000);
        // }

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

@endsection

@push('style')
    <style>
        .input-group-text {
            color: #ffffff;
            border: unset;
        }

        .form-control:focus {
            box-shadow: unset;
            border: 1px solid rgba(224, 224, 224, 0.5)
        }

        .input-group-text:focus {
            box-shadow: none !important;
        }

        .reply-bg {
            background-color: #ffd96729
        }

        .empty-message img {
            width: 120px;
            margin-bottom: 15px;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addAttachment').on('click', function() {
                fileAdded++;
                if (fileAdded == 5) {
                    $(this).attr('disabled', true)
                }
                $(".fileUploadsContainer").append(`
                    <div class="col-lg-4 col-md-12 removeFileInput">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="file" name="attachments[]" class="form-control form--control" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" required>
                                <button type="button" class="input-group-text removeFile bg--danger"><i class="las la-times"></i></button>
                            </div>
                        </div>
                    </div>
                `)
            });
            $(document).on('click', '.removeFile', function() {
                $('.addAttachment').removeAttr('disabled', true)
                fileAdded--;
                $(this).closest('.removeFileInput').remove();
            });
        })(jQuery);
    </script>
@endpush
