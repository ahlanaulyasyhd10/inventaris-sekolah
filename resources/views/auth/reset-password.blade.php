@extends('layouts.guest')
@section('title', 'Reset Kata Sandi')

@section('content')
    <h2 class="h2 text-center mb-4">Atur ulang kata sandi</h2>

    <form method="POST" action="{{ route('password.store') }}" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="mb-3">
            <label class="form-label required" for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan email akun" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label required" for="password">Kata sandi baru</label>
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required autocomplete="new-password">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label required" for="password_confirmation">Konfirmasi kata sandi</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru" required autocomplete="new-password">
        </div>
        <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Simpan kata sandi</button>
        </div>
    </form>
@endsection
