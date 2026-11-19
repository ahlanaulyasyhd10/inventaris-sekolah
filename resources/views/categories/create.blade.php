@extends('layouts.app')
@section('title', 'Tambah Kategori')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Kategori' => route('admin.categories.index'), 'Tambah' => null]])
@endsection

@section('content')
    <div class="row"><div class="col-lg-6">
        <form class="card" method="POST" action="{{ route('admin.categories.store') }}" novalidate>
            @csrf
            @include('categories._form')
        </form>
    </div></div>
@endsection
