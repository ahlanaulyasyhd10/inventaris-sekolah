@extends('layouts.app')
@section('title', 'Daftar Barang')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Daftar Barang' => null]])
@endsection

@section('content')
    <livewire:item-catalog />
@endsection
