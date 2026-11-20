@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    @if ($user->kyc_data)
                        <ul class="list-group">
                            @foreach ($user->kyc_data as $val)
                                @continue(!$val->value)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">
                                        {{ __($val->name) }}
                                    </span>
                                
                                    <span class="d-flex gap-2">
                                        @if ($val->type == 'checkbox')
                                            <span class="text-muted">
                                                {{ implode(', ', $val->value) }}
                                            </span>
                                
                                        @elseif ($val->type == 'file')
                                            @if ($val->value)
                                                @php
                                                    $imgUrl  = route('admin.view.attachment', encrypt(getFilePath('verify') . '/' . $val->value));
                                                    $dlUrl   = route('admin.download.attachment', encrypt(getFilePath('verify') . '/' . $val->value));
                                                    $ext     = strtolower(pathinfo($val->value, PATHINFO_EXTENSION));
                                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                @endphp

                                                @if ($isImage)
                                                    <div class="d-flex flex-column gap-2 w-100 mt-2">
                                                        <div class="kyc-img-wrap position-relative d-inline-block" style="max-width:340px;">
                                                            <img src="{{ $imgUrl }}"
                                                                 class="kyc-detect-img img-fluid rounded border"
                                                                 crossorigin="anonymous"
                                                                 style="display:block;">
                                                            <canvas class="kyc-detect-canvas position-absolute top-0 start-0 w-100 h-100"
                                                                    style="pointer-events:none;"></canvas>
                                                            <span class="kyc-face-badge badge position-absolute top-0 end-0 m-1 bg-secondary">
                                                                @lang('Detecting…')
                                                            </span>
                                                        </div>
                                                        <div class="d-flex gap-2">
                                                            <a href="{{ $imgUrl }}" target="_blank"
                                                               class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                                                                <i class="fa-regular fa-eye"></i>
                                                                <span class="d-none d-md-inline">@lang('View')</span>
                                                            </a>
                                                            <a href="{{ $dlUrl }}"
                                                               class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                                                                <i class="fa-solid fa-download"></i>
                                                                <span class="d-none d-md-inline">@lang('Download')</span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ $imgUrl }}" target="_blank"
                                                           class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                                                            <i class="fa-regular fa-eye"></i>
                                                            <span class="d-none d-md-inline">@lang('View')</span>
                                                        </a>
                                                        <a href="{{ $dlUrl }}"
                                                           class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                                                            <i class="fa-solid fa-download"></i>
                                                            <span class="d-none d-md-inline">@lang('Download')</span>
                                                        </a>
                                                    </div>
                                                @endif
                                            @else
                                                <span class="badge bg-light text-muted">
                                                    @lang('No File')
                                                </span>
                                            @endif
                                
                                        @else
                                            <span class="text-muted">
                                                {{ __($val->value) }}
                                            </span>
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <h5 class="text-center">@lang('KYC data not found')</h5>
                    @endif
                    @if ($user->kv == Status::KYC_REJECTED)
                        <div class="my-3">
                            <h6>@lang('Rejection Reason')</h6>
                            <p>{{ $user->kyc_rejection_reason }}</p>
                        </div>
                        @if ($user->kyc_rejection_attachments)
                            <div class="mt-3">
                                <h6 class="fw-semibold">@lang('Rejection Attachments')</h6>
                                <ul class="list-group">
                                    @foreach ($user->kyc_rejection_attachments as $file)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ basename($file) }}</span>
                        
                                            <div class="d-flex gap-2">
                                                <a target="_blank"
                                                   href="{{ route('admin.view.attachment', encrypt(getFilePath('verify') . '/' . $file)) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fa-regular fa-eye"></i>
                                                    @lang('View')
                                                </a>
                        
                                                <a href="{{ route('admin.download.attachment', encrypt(getFilePath('verify') . '/' . $file)) }}"
                                                   class="btn btn-sm btn-outline-secondary">
                                                    <i class="fa-solid fa-download"></i>
                                                    @lang('Download')
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    @endif
                    @if ($user->kv == Status::KYC_VERIFIED)
                        <div class="my-3">
                            <h6>@lang('Aproval Reason')</h6>
                            <p>{{ $user->kyc_approve_reason }}</p>
                        </div>
                        @if ($user->kyc_approve_attachments)
                            <div class="mt-3">
                                <h6 class="fw-semibold">@lang('Aproval Attachments')</h6>
                                <ul class="list-group">
                                    @foreach ($user->kyc_approve_attachments as $file)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ basename($file) }}</span>
                        
                                            <div class="d-flex gap-2">
                                                <a target="_blank"
                                                   href="{{ route('admin.view.attachment', encrypt(getFilePath('verify') . '/' . $file)) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fa-regular fa-eye"></i>
                                                    @lang('View')
                                                </a>
                        
                                                <a href="{{ route('admin.download.attachment', encrypt(getFilePath('verify') . '/' . $file)) }}"
                                                   class="btn btn-sm btn-outline-secondary">
                                                    <i class="fa-solid fa-download"></i>
                                                    @lang('Download')
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    @endif
                    @if ($user->kv == Status::KYC_PENDING)
                        <div class="d-flex flex-wrap justify-content-end mt-3">
                            <button class="btn btn-outline--danger me-3" data-bs-toggle="modal"
                                data-bs-target="#kycRejectionModal"><i class="las la-ban"></i>@lang('Reject')</button>
                            <button class="btn btn-outline--success" data-bs-toggle="modal" data-bs-target="#kycApproveModal" ><i
                                    class="las la-check"></i>@lang('Approve')</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="kycRejectionModal" class="modal fade">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Reject KYC Documents')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
    
                <form action="{{ route('admin.users.kyc.reject', $user->id) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
    
                    <div class="modal-body">
                        <div class="alert alert-primary p-3">
                            @lang('If you reject these documents, the user can re-submit new ones.')
                        </div>
    
                        <div class="form-group mb-3">
                            <label>@lang('Rejection Reason')</label>
                            <textarea class="form-control"
                                      name="reason"
                                      rows="4"
                                      required></textarea>
                        </div>
    
                        <div class="form-group">
                            <label>@lang('Attachments (optional)')</label>
                            <input type="file"
                                   name="attachments[]"
                                   class="form-control"
                                   multiple
                                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            <small class="text-muted">
                                @lang('You may attach screenshots or reference documents.')
                            </small>
                        </div>
                    </div>
    
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--danger w-100">
                            @lang('Reject KYC')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div id="kycApproveModal" class="modal fade">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Approve KYC Documents')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
    
                <form action="{{ route('admin.users.kyc.approve', $user->id) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
    
                    <div class="modal-body">
                        <div class="alert alert-primary p-3">
                            @lang('Approve Kyc Documents.')
                        </div>
    
                        <div class="form-group mb-3">
                            <label>@lang('Approval Reason')</label>
                            <textarea class="form-control"
                                      name="reason"
                                      rows="4"
                                      ></textarea>
                        </div>
    
                        <div class="form-group">
                            <label>@lang('Attachments (optional)')</label>
                            <input type="file"
                                   name="attachments[]"
                                   class="form-control"
                                   multiple
                                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            <small class="text-muted">
                                @lang('You may attach screenshots or reference documents.')
                            </small>
                        </div>
                    </div>
    
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--success w-100">
                            @lang('Approve KYC')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
(async function () {
    var MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';
    try {
        await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
    } catch (e) {
        console.warn('face-api.js model failed to load', e);
        return;
    }

    document.querySelectorAll('.kyc-img-wrap').forEach(function (wrap) {
        var img    = wrap.querySelector('.kyc-detect-img');
        var canvas = wrap.querySelector('.kyc-detect-canvas');
        var badge  = wrap.querySelector('.kyc-face-badge');

        async function runDetection() {
            try {
                canvas.width  = img.naturalWidth;
                canvas.height = img.naturalHeight;

                var detections = await faceapi.detectAllFaces(
                    img,
                    new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.4 })
                );

                var resized = faceapi.resizeResults(detections, { width: img.width, height: img.height });
                faceapi.draw.drawDetections(canvas, resized);

                if (detections.length === 0) {
                    badge.textContent = 'No face detected';
                    badge.className   = 'kyc-face-badge badge position-absolute top-0 end-0 m-1 bg-warning text-dark';
                } else {
                    badge.textContent = detections.length + (detections.length === 1 ? ' face' : ' faces') + ' detected';
                    badge.className   = 'kyc-face-badge badge position-absolute top-0 end-0 m-1 bg-success';
                }
            } catch (e) {
                badge.textContent = 'Detection error';
                badge.className   = 'kyc-face-badge badge position-absolute top-0 end-0 m-1 bg-danger';
            }
        }

        if (img.complete && img.naturalWidth > 0) {
            runDetection();
        } else {
            img.addEventListener('load', runDetection);
            img.addEventListener('error', function () {
                badge.textContent = 'Image load error';
                badge.className   = 'kyc-face-badge badge position-absolute top-0 end-0 m-1 bg-danger';
            });
        }
    });
}());
</script>
@endpush
