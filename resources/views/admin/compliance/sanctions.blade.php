@extends('admin.layouts.app')
@section('panel')
<div class="card">
    <div class="card-header"><h5 class="mb-0">Sanctions Screening Hits</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table--light style--two mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Screening Type</th>
                        <th>Provider</th>
                        <th>Match Data</th>
                        <th>Screened At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hits as $hit)
                    <tr>
                        <td>{{ $hit->id }}</td>
                        <td>
                            <a href="{{ route('admin.users.detail', $hit->user_id) }}">
                                {{ optional($hit->user)->fullname }}<br>
                                <small class="text-muted">{{ optional($hit->user)->email }}</small>
                            </a>
                        </td>
                        <td><span class="badge badge--danger">{{ strtoupper($hit->screening_type) }}</span></td>
                        <td>{{ $hit->provider }}</td>
                        <td>
                            @if($hit->match_data)
                                <small>{{ json_encode($hit->match_data) }}</small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $hit->screened_at->format('M d, Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.users.detail', $hit->user_id) }}" class="btn btn-sm btn--primary">View User</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4">No sanctions hits found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($hits->hasPages())
    <div class="card-footer">
        {{ $hits->links() }}
    </div>
    @endif
</div>
@endsection
