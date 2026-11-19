@extends('layouts.app')
@section('title', 'Kategori')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Kategori' => null]])
@endsection
@section('actions')
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i> Tambah Kategori</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-vcenter datatable" id="categories-table">
                <thead>
                <tr><th style="width:60px">No</th><th>Nama Kategori</th><th>Jumlah Barang</th><th class="no-sort text-end" style="width:130px">Aksi</th></tr>
                </thead>
                <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $category->name }}</td>
                        <td data-order="{{ $category->items_count }}">{{ format_number($category->items_count) }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="ti ti-edit"></i></a>
                            @include('partials.delete-button', ['action' => route('admin.categories.destroy', $category), 'message' => 'Kategori "'.$category->name.'" akan dihapus.'])
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
