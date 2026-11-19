@extends('layouts.app')
@section('title', 'Ubah Kategori')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Kategori' => route('admin.categories.index'), 'Ubah' => null]])
@endsection

@section('content')
    <div class="row"><div class="col-lg-6">
        <form class="card" method="POST" action="{{ route('admin.categories.update', $category) }}" novalidate>
            @csrf
            @method('PUT')
            @include('categories._form')
        </form>
    </div></div>
@endsection
