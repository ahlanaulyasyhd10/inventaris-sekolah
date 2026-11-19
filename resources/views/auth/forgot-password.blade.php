@extends('layouts.guest')
@section('title', 'Lupa Kata Sandi')

@section('content')
    <h2 class="h2 text-center mb-2">Lupa kata sandi</h2>
    <p class="text-secondary mb-4">Masukkan email akunmu, kami akan mengirim tautan untuk mengatur ulang kata sandi.</p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label class="form-label required" for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan email akun" value="{{ old('email') }}" required autofocus>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Kirim tautan reset</button>
        </div>
    </form>
@endsection

@section('below')
    <div class="text-center text-secondary mt-3"><a href="{{ route('login') }}">Kembali ke halaman masuk</a></div>
@endsection
