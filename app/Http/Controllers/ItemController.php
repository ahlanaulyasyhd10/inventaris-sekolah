<?php

namespace App\Http\Controllers;

use App\Exports\ItemsExport;
use App\Exports\ItemsTemplateExport;
use App\Http\Requests\ImportItemsRequest;
use App\Http\Requests\StoreItemRequest;
use App\Imports\ItemsImport;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Loan;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class ItemController extends Controller
{
    public function index(): View
    {
        return view('items.index', [
            'items' => Item::with('category')->orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'conditions' => Item::CONDITIONS,
        ]);
    }

    public function create(): View
    {
        return view('items.create', [
            'categories' => Category::orderBy('name')->get(),
            'conditions' => Item::CONDITIONS,
        ]);
    }

    public function store(StoreItemRequest $request): RedirectResponse
    {
        $data = collect($request->validated())->except(['photo', 'remove_photo'])->all();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('items', 'public');
        }

        $item = Item::create($data);

        ActivityLogger::log('create_item', "Menambah barang {$item->name} ({$item->code}).");

        return redirect()->route('admin.items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(Item $item): View
    {
        $item->load('category');

        return view('items.show', [
            'item' => $item,
            'loans' => $item->loans()->with('user')->latest()->take(10)->get(),
        ]);
    }

    public function edit(Item $item): View
    {
        return view('items.edit', [
            'item' => $item,
            'categories' => Category::orderBy('name')->get(),
            'conditions' => Item::CONDITIONS,
        ]);
    }

    public function update(UpdateItemRequest $request, Item $item): RedirectResponse
    {
        $data = collect($request->validated())->except(['photo', 'remove_photo'])->all();

        if ($request->hasFile('photo')) {
            $this->deletePhoto($item);
            $data['photo'] = $request->file('photo')->store('items', 'public');
        } elseif ($request->boolean('remove_photo')) {
            $this->deletePhoto($item);
            $data['photo'] = null;
        }

        $item->update($data);

        ActivityLogger::log('update_item', "Mengubah data barang {$item->name} ({$item->code}).");

        return redirect()->route('admin.items.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $hasOpenLoan = $item->loans()
            ->whereIn('status', [Loan::STATUS_PENDING, ...Loan::ACTIVE_STATUSES])
            ->exists();

        if ($hasOpenLoan) {
            return back()->with('error', "Barang {$item->name} masih memiliki peminjaman yang belum selesai.");
        }

        $item->delete(); // Soft Delete: riwayat peminjaman tetap utuh.

        ActivityLogger::log('delete_item', "Menghapus barang {$item->name} ({$item->code}).");

        return redirect()->route('admin.items.index')->with('success', 'Barang berhasil dihapus.');
    }

    public function exportExcel(): Response
    {
        $items = Item::with('category')->orderBy('code')->get();
        ActivityLogger::log('export_items', 'Mengekspor data barang ke Excel.');

        return Excel::download(new ItemsExport($items), 'data-barang-'.now()->format('Ymd-His').'.xlsx');
    }

    public function exportPdf(): Response
    {
        $items = Item::with('category')->orderBy('code')->get();
        ActivityLogger::log('export_items', 'Mengekspor data barang ke PDF.');

        return Pdf::loadView('reports.items', compact('items'))->setPaper('a4')->download('data-barang-'.now()->format('Ymd-His').'.pdf');
    }

    public function template(): Response
    {
        return Excel::download(new ItemsTemplateExport, 'template-impor-barang.xlsx');
    }

    public function import(ImportItemsRequest $request): RedirectResponse
    {
        $import = new ItemsImport;
        Excel::import($import, $request->file('file'));

        ActivityLogger::log('import_items', "Mengimpor barang dari berkas: {$import->created} ditambah, {$import->updated} diperbarui.");

        $message = "Impor selesai: {$import->created} barang ditambahkan, {$import->updated} diperbarui.";

        if ($import->errors) {
            return redirect()->route('admin.items.index')
                ->with('success', $message)
                ->with('import_errors', array_slice($import->errors, 0, 10));
        }

        return redirect()->route('admin.items.index')->with('success', $message);
    }

    public function labels(Request $request): View
    {
        return view('items.labels', [
            'items' => Item::with('category')
                ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->category))
                ->orderBy('code')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    /** Hapus foto unggahan barang (kembali ke gambar bawaan). Dipanggil setelah konfirmasi pop-up. */
    public function destroyPhoto(Item $item): RedirectResponse
    {
        if ($item->photo) {
            $this->deletePhoto($item);
            $item->update(['photo' => null]);
            ActivityLogger::log('update_item', "Menghapus foto barang {$item->name} ({$item->code}).");
        }

        return redirect()->route('admin.items.edit', $item)->with('success', 'Foto barang berhasil dihapus.');
    }

    private function deletePhoto(Item $item): void
    {
        if ($item->photo) {
            Storage::disk('public')->delete($item->photo);
        }
    }
}
