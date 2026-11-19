@extends('layouts.guest')
@section('title', 'Konfirmasi Kata Sandi')

@section('content')
    <h2 class="h2 text-center mb-2">Konfirmasi kata sandi</h2>
    <p class="text-secondary mb-4">Ini area aman. Masukkan kata sandimu untuk melanjutkan.</p>

    <form method="POST" action="{{ route('password.confirm') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label class="form-label required" for="password">Kata sandi</label>
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan kata sandi" required autofocus autocomplete="current-password">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Konfirmasi</button>
        </div>
    </form>
@endsection
