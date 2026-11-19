@extends('layouts.app')
@section('title', 'Ubah User')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Manajemen User' => route('admin.users.index'), 'Ubah' => null]])
@endsection

@section('content')
    <div class="row"><div class="col-lg-8">
        <form class="card" method="POST" action="{{ route('admin.users.update', $user) }}" autocomplete="off" novalidate>
            @csrf
            @method('PUT')
            @include('users._form')
        </form>
    </div></div>
@endsection
