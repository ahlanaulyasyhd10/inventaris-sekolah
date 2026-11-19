@extends('layouts.guest')
@section('title', 'Verifikasi Email')

@section('content')
    <h2 class="h2 text-center mb-2">Verifikasi email</h2>
    <p class="text-secondary mb-4">Kami sudah mengirim tautan verifikasi ke emailmu. Jika belum menerima, kami dapat mengirim ulang.</p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success">Tautan verifikasi baru sudah dikirim ke emailmu.</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
        @csrf
        <button type="submit" class="btn btn-primary w-100">Kirim ulang email verifikasi</button>
    </form>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-link w-100">Keluar</button>
    </form>
@endsection
