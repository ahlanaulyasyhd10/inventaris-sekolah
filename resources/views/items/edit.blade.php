@extends('layouts.app')
@section('title', 'Ubah Barang')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Barang' => route('admin.items.index'), 'Ubah' => null]])
@endsection

@section('content')
    <div class="row"><div class="col-lg-8">
        <form class="card" method="POST" action="{{ route('admin.items.update', $item) }}" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')
            @include('items._form')
        </form>
        @if ($item->photo)
            <form id="delete-photo-form" method="POST" action="{{ route('admin.items.photo.destroy', $item) }}" class="d-none">@csrf @method('DELETE')</form>
        @endif
    </div></div>
@endsection
