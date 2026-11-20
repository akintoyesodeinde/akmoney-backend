@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form method="POST" action="{{ route('admin.setting.security') }}">
                    @csrf
                    <div class="card-header">
                        <h5 class="card-title mb-0">@lang('IP Access Control')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-bold">@lang('IP Blacklist')</label>
                                    <small class="d-block text-muted mb-2">
                                        @lang('Block these IPs from accessing the site. One IP per line, or comma-separated.') <br>
                                        @lang('Supports exact IPs (192.168.1.1), CIDR ranges (192.168.1.0/24), and wildcards (192.168.1.*).')
                                    </small>
                                    <textarea name="ip_blacklist" class="form-control" rows="8"
                                        placeholder="192.168.1.1&#10;10.0.0.0/8&#10;172.16.0.*">{{ old('ip_blacklist', $general->ip_blacklist) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-bold">@lang('IP Whitelist')</label>
                                    <small class="d-block text-muted mb-2">
                                        @lang('Only allow these IPs to access the site. Leave empty to allow all IPs (except blacklisted).') <br>
                                        @lang('Supports exact IPs, CIDR ranges, and wildcards.')
                                        <strong class="text-danger">@lang('Warning: enabling the whitelist will block everyone not on the list.')</strong>
                                    </small>
                                    <textarea name="ip_whitelist" class="form-control" rows="8"
                                        placeholder="203.0.113.5&#10;198.51.100.0/24">{{ old('ip_whitelist', $general->ip_whitelist) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Save Security Settings')</button>
                    </div>
                </form>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Rate Limiting & Bot Protection Summary')</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Protection')</th>
                                    <th>@lang('Scope')</th>
                                    <th>@lang('Limit')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>@lang('Rate Limiting — Login')</td>
                                    <td>@lang('User & API')</td>
                                    <td>@lang('10 requests / 1 min per IP')</td>
                                    <td><span class="badge badge--success">@lang('Active')</span></td>
                                </tr>
                                <tr>
                                    <td>@lang('Rate Limiting — Register')</td>
                                    <td>@lang('User & API')</td>
                                    <td>@lang('5 requests / 1 min per IP')</td>
                                    <td><span class="badge badge--success">@lang('Active')</span></td>
                                </tr>
                                <tr>
                                    <td>@lang('Rate Limiting — Password Reset')</td>
                                    <td>@lang('User & API')</td>
                                    <td>@lang('5 requests / 1 min per IP')</td>
                                    <td><span class="badge badge--success">@lang('Active')</span></td>
                                </tr>
                                <tr>
                                    <td>@lang('Account Lockout')</td>
                                    <td>@lang('Login')</td>
                                    <td>@lang('After 5 failed attempts')</td>
                                    <td><span class="badge badge--success">@lang('Active')</span></td>
                                </tr>
                                <tr>
                                    <td>@lang('reCAPTCHA v3 (Score ≥ 0.5)')</td>
                                    <td>@lang('All Auth Forms')</td>
                                    <td>—</td>
                                    <td>
                                        @if(\App\Models\Extension::where('act','google-recaptcha3')->where('status',\App\Constants\Status::ENABLE)->exists())
                                            <span class="badge badge--success">@lang('Active')</span>
                                        @else
                                            <span class="badge badge--warning">@lang('Disabled — enable in Extensions')</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('HSTS / Security Headers')</td>
                                    <td>@lang('All Pages')</td>
                                    <td>@lang('1 year max-age')</td>
                                    <td><span class="badge badge--success">@lang('Active')</span></td>
                                </tr>
                                <tr>
                                    <td>@lang('Force HTTPS')</td>
                                    <td>@lang('All Pages')</td>
                                    <td>—</td>
                                    <td><span class="badge badge--success">@lang('Active (production only)')</span></td>
                                </tr>
                                <tr>
                                    <td>@lang('IP Blacklist')</td>
                                    <td>@lang('All Pages')</td>
                                    <td>—</td>
                                    <td>
                                        @if($general->ip_blacklist)
                                            <span class="badge badge--success">@lang('Active')</span>
                                        @else
                                            <span class="badge badge--secondary">@lang('No IPs configured')</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('IP Whitelist')</td>
                                    <td>@lang('All Pages')</td>
                                    <td>—</td>
                                    <td>
                                        @if($general->ip_whitelist)
                                            <span class="badge badge--danger">@lang('Active — others are blocked')</span>
                                        @else
                                            <span class="badge badge--secondary">@lang('Disabled')</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
