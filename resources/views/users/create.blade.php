@extends('layouts.app')
@section('title', 'Tambah User')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Manajemen User' => route('admin.users.index'), 'Tambah' => null]])
@endsection

@section('content')
    <div class="row"><div class="col-lg-8">
        <form class="card" method="POST" action="{{ route('admin.users.store') }}" autocomplete="off" novalidate>
            @csrf
            @include('users._form')
        </form>
    </div></div>
@endsection
