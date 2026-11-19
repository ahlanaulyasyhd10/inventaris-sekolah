@extends('layouts.app')
@section('title', 'Manajemen User')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Manajemen User' => null]])
@endsection
@section('actions')
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i> Tambah User</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-body border-bottom py-3">
            <div class="row g-2">
                <div class="col-md-3">
                    <select class="form-select" data-table-filter="#users-table" data-column="3" aria-label="Filter role">
                        <option value="">Semua role</option>
                        <option value="Admin">Admin</option>
                        <option value="User">User</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-vcenter datatable" id="users-table">
                <thead>
                <tr><th style="width:50px">No</th><th>Nama</th><th>Email</th><th>Role</th><th>Total Peminjaman</th><th class="no-sort text-end" style="width:130px">Aksi</th></tr>
                </thead>
                <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @include('partials.avatar', ['user' => $user, 'size' => 'sm'])
                                <span class="ms-2">{{ $user->name }} @if ($user->is(auth()->user())) <span class="badge bg-blue-lt">Kamu</span> @endif</span>
                            </div>
                        </td>
                        <td>
                            <div>{{ $user->email }}</div>
                            @if (whatsapp_url($user->phone))
                                <a href="{{ whatsapp_url($user->phone) }}" target="_blank" rel="noopener" class="small"><i class="ti ti-brand-whatsapp"></i> {{ $user->phone }}</a>
                            @else
                                <span class="text-secondary small">Belum ada nomor WhatsApp</span>
                            @endif
                        </td>
                        <td><span class="badge {{ $user->isAdmin() ? 'bg-purple-lt' : 'bg-secondary-lt' }}">{{ $user->isAdmin() ? 'Admin' : 'User' }}</span></td>
                        <td data-order="{{ $user->loans_count }}">{{ format_number($user->loans_count) }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="ti ti-edit"></i></a>
                            @unless ($user->is(auth()->user()))
                                @include('partials.delete-button', ['action' => route('admin.users.destroy', $user), 'message' => 'Akun "'.$user->name.'" akan dihapus.'])
                            @endunless
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
