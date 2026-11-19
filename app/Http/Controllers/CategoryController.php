<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('categories.index', [
            'categories' => Category::withCount('items')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $category = Category::create($request->validated());

        ActivityLogger::log('create_category', "Menambah kategori {$category->name}.");

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        ActivityLogger::log('update_category', "Mengubah kategori menjadi {$category->name}.");

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Barang yang sudah di-soft delete tetap merujuk ke kategori, jadi ikut diperiksa.
        if ($category->items()->withTrashed()->exists()) {
            return back()->with('error', "Kategori {$category->name} masih memiliki barang sehingga tidak dapat dihapus.");
        }

        $category->delete();

        ActivityLogger::log('delete_category', "Menghapus kategori {$category->name}.");

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
