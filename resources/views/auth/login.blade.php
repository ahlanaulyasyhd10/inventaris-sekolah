@extends('layouts.guest')
@section('title', 'Masuk')

@section('content')
    <h2 class="h2 text-center mb-4">Masuk ke akunmu</h2>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" autocomplete="off" novalidate>
        @csrf
        <div class="mb-3">
            <label class="form-label required" for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="contoh: admin@sekolah.test" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-2">
            <label class="form-label required" for="password">
                Kata sandi
                <span class="form-label-description"><a href="{{ route('password.request') }}">Lupa kata sandi?</a></span>
            </label>
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan kata sandi" required autocomplete="current-password">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-2">
            <label class="form-check">
                <input type="checkbox" name="remember" class="form-check-input">
                <span class="form-check-label">Ingat saya di perangkat ini</span>
            </label>
        </div>
        <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Masuk</button>
        </div>
    </form>
@endsection

@section('below')
    <div class="text-center text-secondary mt-3">
        Belum punya akun? <a href="{{ route('register') }}" tabindex="-1">Daftar</a>
    </div>
@endsection
