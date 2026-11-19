@extends('layouts.app')
@section('title', 'Profil Saya')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Profil Saya' => null]])
@endsection

@section('content')
    <div class="row row-cards">
        <div class="col-lg-6">
            <form class="card" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PATCH')
                <div class="card-header"><h3 class="card-title">Informasi Profil</h3></div>
                <div class="card-body">
                    @if (session('status') === 'profile-updated')
                        <div class="alert alert-success">Profil berhasil diperbarui.</div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Foto profil</label>
                        @include('partials.photo-field', [
                            'url' => $user->photoUrl(),
                            'hasPhoto' => (bool) $user->photo,
                            'shape' => 'circle',
                            'height' => 96,
                            'placeholderUser' => $user,
                            'deleteFormId' => $user->photo ? 'delete-profile-photo-form' : null,
                            'deleteText' => 'Foto profil akan dihapus dan kembali menampilkan inisial namamu.',
                            'hint' => 'JPG, PNG, atau WEBP, maksimal 5 MB.',
                            'changeLabel' => 'Ubah foto profil',
                        ])
                    </div>
                    <div class="mb-3">
                        <label class="form-label required" for="name">Nama</label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Masukkan nama lengkap" value="{{ old('name', $user->name) }}" required autofocus>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required" for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan email" value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="phone">No. WhatsApp</label>
                        <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Contoh: 081234567890" value="{{ old('phone', $user->phone) }}">
                        <div class="form-hint">Opsional. Dipakai Admin untuk menghubungi jika ada keperluan peminjaman.</div>
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control" placeholder="Role" value="{{ $user->isAdmin() ? 'Admin' : 'User' }}" disabled>
                    </div>
                </div>
                <div class="card-footer text-end"><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>

        <div class="col-lg-6">
            <form class="card" method="POST" action="{{ route('password.update') }}" novalidate>
                @csrf
                @method('PUT')
                <div class="card-header"><h3 class="card-title">Ubah Kata Sandi</h3></div>
                <div class="card-body">
                    @if (session('status') === 'password-updated')
                        <div class="alert alert-success">Kata sandi berhasil diubah.</div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label required" for="current_password">Kata sandi saat ini</label>
                        <input type="password" id="current_password" name="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" placeholder="Masukkan kata sandi saat ini" autocomplete="current-password">
                        @error('current_password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required" for="new_password">Kata sandi baru</label>
                        <input type="password" id="new_password" name="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" placeholder="Minimal 8 karakter" autocomplete="new-password">
                        @error('password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label required" for="password_confirmation">Konfirmasi kata sandi baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru" autocomplete="new-password">
                    </div>
                </div>
                <div class="card-footer text-end"><button type="submit" class="btn btn-primary">Ubah Kata Sandi</button></div>
            </form>
        </div>
    </div>
    @if ($user->photo)
        <form id="delete-profile-photo-form" method="POST" action="{{ route('profile.photo.destroy') }}" class="d-none">@csrf @method('DELETE')</form>
    @endif
@endsection
