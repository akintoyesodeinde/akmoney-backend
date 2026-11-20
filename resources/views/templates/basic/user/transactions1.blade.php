@extends($activeTemplate . 'layouts.master')
@section('content')
<section class="dashboard-section body-collapse transactions">
    <div class="overlay pt-120">
        <div class="container-fruid">
            <div class="head-area">
                    <div class="row">
                        <div class="col-lg-5 col-md-4">
                            <h4>Transactions</h4>
                        </div>
                        <div class="col-lg-7 col-md-8">
                            <!--<div class="transactions-right d-flex align-items-center">-->
                            <!--    <form action="#" class="flex-fill">-->
                            <!--        <div class="form-group d-flex align-items-center">-->
                            <!--            <img src="assets/images/icon/search.png" alt="icon">-->
                            <!--            <input type="text" placeholder="Type to search...">-->
                            <!--        </div>-->
                            <!--    </form>-->
                            <!--</div>-->
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="transactions-main">
                            <div class="top-items">
                                <h6>All Transactions</h6>
                            </div>
                            <div class="filters-item">
                                <form>
                                    <div class=" form-group d-flex align-items-center">
                                        <img src="/assets/images/search.png" alt="icon">
                                        <input type="text" placeholder="Type to search..." name="search">
                                    </div>
                                    <div class="">
                                        <select class="form-select form--select"
                                                    data-minimum-results-for-search="-1" name="trx_type">
                                            <option value="" selected>Transaction Type</option
                                            <option value="+" @selected(request()->trx_type == '+')>@lang('Plus')</option>
                                            <option value="-" @selected(request()->trx_type == '-')>@lang('Minus') </option>
                                        </select>
                                    </div>
                                    <div class="">
                                        <select class="form-select form--select"
                                                    data-minimum-results-for-search="-1" name="remark">
                                            <option value="">Select Remark</option>
                                            @foreach ($remarks as $remark)-->
                                               <option value="{{ $remark->remark }}" @selected(request()->remark == $remark->remark)>
                                                  {{ __(keyToTitle($remark->remark)) }}</option>
                                          @endforeach
                                        </select>
                                    </div>
                                    <div class="single-item">
                                        <button>Filter</button>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Trx Number</th>
                                            <th scope="col">Amount</th>
                                            <th scope="col">Post Balance</th>
                                            <th scope="col">Time</th>
                                            <th scope="col">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($transactions as $transaction)
                                        <tr data-bs-toggle="modal" data-bs-target="#transactionsMod">
                                            <th scope="row">
                                                <p>{{ $transaction->trx }}</p>
                                            </th>
                                            <td>
                                                <p>@if ($transaction->trx_type == '+')
                                                        +{{ showAmount($transaction->amount) }}
                                                    @else
                                                        -{{ showAmount($transaction->amount) }}
                                                    @endif
                                            </p>
                                                <p class="mdr">{{ showAmount($transaction->charge) }}</p>
                                            </td>
                                            <td>
                                                <p>{{ showAmount($transaction->post_balance) }}</p>
                                            </td>
                                            <td>
                                                <p class="">{{ showDateTime($transaction->created_at) }}</p>
                                            </td>
                                            <td>
                                                <p>{{ $transaction->details }}</p>
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="100%" class="text-muted text-center">{{ __($emptyMessage) }}</td>
                                            </tr>
                                       @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!--<nav aria-label="Page navigation" class="d-flex justify-content-center mt-40">-->
                            <!--    <ul class="pagination justify-content-center align-items-center mb-40">-->
                            <!--        <li class="page-item">-->
                            <!--            <a class="page-link previous" href="javascript:void(0)" aria-label="Previous">-->
                            <!--                <i class="fa-solid fa-angles-left"></i>-->
                            <!--            </a>-->
                            <!--        </li>-->
                            <!--        <li class="page-item">-->
                            <!--            <a class="page-link previous" href="javascript:void(0)" aria-label="Previous">-->
                            <!--                <i class="fa-solid fa-angle-left"></i>-->
                            <!--            </a>-->
                            <!--        </li>-->
                            <!--        <li class="page-item"><a class="page-link" href="javascript:void(0)">1</a></li>-->
                            <!--        <li class="page-item"><a class="page-link active" href="javascript:void(0)">2</a></li>-->
                            <!--        <li class="page-item"><a class="page-link" href="javascript:void(0)">3</a></li>-->
                            <!--        <li class="page-item"><a class="page-link" href="javascript:void(0)">...</a></li>-->
                            <!--        <li class="page-item">-->
                            <!--            <a class="page-link next" href="javascript:void(0)" aria-label="Next">-->
                            <!--                <i class="fa-solid fa-angle-right"></i>-->
                            <!--            </a>-->
                            <!--        </li>-->
                            <!--        <li class="page-item">-->
                            <!--            <a class="page-link next" href="javascript:void(0)" aria-label="Next">-->
                            <!--                <i class="fa-solid fa-angles-right"></i>-->
                            <!--            </a>-->
                            <!--        </li>-->
                            <!--    </ul>-->
                            <!--</nav>-->
                        </div>
                    </div>
                </div>
            <!--<div class="row g-4 justify-content-center">-->
            <!--    <div class="col-12">-->
            <!--        <div class="show-filter text-end mb-3">-->
            <!--            <button type="button" class="btn btn--base showFilterBtn btn-sm"><i class="las la-filter"></i>-->
            <!--                @lang('Filter')</button>-->
            <!--        </div>-->
            <!--        <div class="card custom--card responsive-filter-card mb-4">-->
            <!--            <div class="card-body">-->
            <!--                <form>-->
            <!--                    <div class="d-flex flex-wrap gap-4">-->
            <!--                        <div class="flex-grow-1">-->
            <!--                            <label>@lang('Transaction Number')</label>-->
            <!--                            <input type="text" name="search" value="{{ request()->search }}"-->
            <!--                                class="form-control form--control">-->
            <!--                        </div>-->
            <!--                        <div class="flex-grow-1">-->
            <!--                            <div class="form-group select2-parent">-->
            <!--                                <label>@lang('Type')</label>-->
            <!--                                <select name="trx_type" class="form-select form--select select2-basic"-->
            <!--                                    data-minimum-results-for-search="-1">-->
            <!--                                    <option value="">@lang('All')</option>-->
            <!--                                    <option value="+" @selected(request()->trx_type == '+')>@lang('Plus')-->
            <!--                                    </option>-->
            <!--                                    <option value="-" @selected(request()->trx_type == '-')>@lang('Minus')-->
            <!--                                    </option>-->
            <!--                                </select>-->
            <!--                            </div>-->
            <!--                        </div>-->
            <!--                        <div class="flex-grow-1">-->
            <!--                            <div class="form-group">-->
            <!--                                <label>@lang('Remark')</label>-->
            <!--                                <select class="form-select form--select select2-basic"-->
            <!--                                    data-minimum-results-for-search="-1" name="remark">-->
            <!--                                    <option value="">@lang('Any')</option>-->
            <!--                                    @foreach ($remarks as $remark)-->
            <!--                                        <option value="{{ $remark->remark }}" @selected(request()->remark == $remark->remark)>-->
            <!--                                            {{ __(keyToTitle($remark->remark)) }}</option>-->
            <!--                                    @endforeach-->
            <!--                                </select>-->
            <!--                            </div>-->
            <!--                        </div>-->
            <!--                        <div class="flex-grow-1 align-self-end">-->
            <!--                            <button class="btn btn--xl btn--base w-100">-->
            <!--                                <i class="las la-filter"></i>-->
            <!--                                @lang('Filter')-->
            <!--                            </button>-->
            <!--                        </div>-->
            <!--                    </div>-->
            <!--                </form>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--    <div class="col-md-12">-->
            <!--        <div class="table-responsive--md">-->
            <!--            <table class="custom--table table">-->
            <!--                <thead>-->
            <!--                    <tr>-->
            <!--                        <th>@lang('Trx Number')</th>-->
            <!--                        <th>@lang('Amount')</th>-->
            <!--                        <th>@lang('Charge')</th>-->
            <!--                        <th>@lang('Post Balance')</th>-->
            <!--                        <th>@lang('Time')</th>-->
            <!--                        <th>@lang('Details')</th>-->
            <!--                    </tr>-->
            <!--                </thead>-->
            <!--                <tbody>-->
            <!--                    @forelse ($transactions as $transaction)-->
            <!--                        <tr>-->
            <!--                            <td>{{ $transaction->trx }}-->
            <!--                            </td>-->
            <!--                            <td>-->
            <!--                                @if ($transaction->trx_type == '+')-->
            <!--                                    <div class="badge badge--success">-->
            <!--                                        +{{ showAmount($transaction->amount) }}-->
            <!--                                    </div>-->
            <!--                                @else-->
            <!--                                    <div class="badge badge--danger">-->
            <!--                                        -{{ showAmount($transaction->amount) }}-->
            <!--                                    </div>-->
            <!--                                @endif-->
            <!--                            </td>-->
            <!--                            <td>{{ showAmount($transaction->charge) }}</td>-->
            <!--                            <td>{{ showAmount($transaction->post_balance) }}</td>-->
            <!--                            <td>{{ showDateTime($transaction->created_at) }}</td>-->
            <!--                            <td>{{ $transaction->details }}</td>-->
            <!--                        </tr>-->
            <!--                    @empty-->
            <!--                        <tr>-->
            <!--                            <td colspan="100%" class="text-muted text-center">{{ __($emptyMessage) }}</td>-->
            <!--                        </tr>-->
            <!--                    @endforelse-->
            <!--                </tbody>-->
            <!--            </table>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            @if ($transactions->hasPages())
                <div class="mt-4 paginate pagination">
                    {{ paginateLinks($transactions) }}
                </div>
            @endif
        </div>
    </div>
</section>
@endsection

@push('style')
    <style>
        .paginate p {
            margin: 0px !important;
        }
        
        .dashboard-section.transactions .transactions-main .filters-item form {
    display: flex;
    align-items: center;
    gap: 15px; /* spacing between items */
}

.dashboard-section.transactions .transactions-main .filters-item .form-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.filters-item select,
.filters-item input {
    height: 40px;
}

    </style>
@endpush
