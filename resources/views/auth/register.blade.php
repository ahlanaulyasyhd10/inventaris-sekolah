@extends('layouts.guest')
@section('title', 'Daftar')

@section('content')
    <h2 class="h2 text-center mb-4">Buat akun baru</h2>

    <form method="POST" action="{{ route('register') }}" autocomplete="off" novalidate>
        @csrf
        <div class="mb-3">
            <label class="form-label required" for="name">Nama lengkap</label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label required" for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="contoh: nama@sekolah.test" value="{{ old('email') }}" required autocomplete="username">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label required" for="password">Kata sandi</label>
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required autocomplete="new-password">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label required" for="password_confirmation">Konfirmasi kata sandi</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi" required autocomplete="new-password">
        </div>
        <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Daftar</button>
        </div>
    </form>
@endsection

@section('below')
    <div class="text-center text-secondary mt-3">
        Sudah punya akun? <a href="{{ route('login') }}" tabindex="-1">Masuk</a>
    </div>
@endsection
