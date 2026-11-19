@extends('layouts.app')
@section('title', 'Activity Log')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Activity Log' => null]])
@endsection

@section('content')
    <div class="card">
        <div class="card-body border-bottom py-3">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" for="q">Cari deskripsi</label>
                    <input type="text" id="q" name="q" class="form-control" placeholder="Kata kunci aktivitas" value="{{ request('q') }}" autofocus>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="action">Jenis aksi</label>
                    <select id="action" name="action" class="form-select">
                        <option value="">Semua aksi</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" @selected(request('action') === $action)>{{ \App\Models\ActivityLog::labelFor($action) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="from">Dari tanggal</label>
                    <input type="date" id="from" name="from" class="form-control" placeholder="Tanggal awal" value="{{ request('from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="to">Sampai tanggal</label>
                    <input type="date" id="to" name="to" class="form-control" placeholder="Tanggal akhir" value="{{ request('to') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="ti ti-filter me-1"></i> Terapkan</button>
                    <a href="{{ route('admin.activity-logs.index') }}" class="btn">Reset</a>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead><tr><th>Waktu</th><th>User</th><th>Aksi</th><th>Deskripsi</th></tr></thead>
                <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td class="text-nowrap">{{ format_datetime($log->created_at) }}</td>
                        <td>{{ $log->user->name ?? 'Akun terhapus' }}</td>
                        <td><span class="badge {{ $log->actionBadge() }}">{{ $log->actionLabel() }}</span></td>
                        <td>{{ $log->description }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-secondary py-4">Tidak ada aktivitas yang cocok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-secondary">Menampilkan {{ format_number($logs->firstItem() ?? 0) }} - {{ format_number($logs->lastItem() ?? 0) }} dari {{ format_number($logs->total()) }} aktivitas</p>
            <div class="ms-auto">{{ $logs->links() }}</div>
        </div>
    </div>
@endsection
