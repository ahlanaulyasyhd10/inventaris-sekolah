<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(): View
    {
        // Daftar barang dengan pencarian & filter live dirender oleh komponen Livewire ItemCatalog.
        return view('catalog.index');
    }
}
