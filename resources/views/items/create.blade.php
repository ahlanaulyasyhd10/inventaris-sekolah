@extends('layouts.app')
@section('title', 'Tambah Barang')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Barang' => route('admin.items.index'), 'Tambah' => null]])
@endsection

@section('content')
    <div class="row"><div class="col-lg-8">
        <form class="card" method="POST" action="{{ route('admin.items.store') }}" enctype="multipart/form-data" novalidate>
            @csrf
            @include('items._form')
        </form>
    </div></div>
@endsection
