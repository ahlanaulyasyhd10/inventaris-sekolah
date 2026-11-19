<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Katalog barang untuk User: pencarian dan filter (kategori, kondisi, ketersediaan stok)
 * berjalan live tanpa reload halaman.
 */
class ItemCatalog extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $category = '';

    public string $condition = '';

    public string $availability = '';

    public function updated(string $property): void
    {
        // Kembali ke halaman pertama setiap kali pencarian/filter berubah.
        if (in_array($property, ['search', 'category', 'condition', 'availability'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'category', 'condition', 'availability']);
        $this->resetPage();
    }

    public function render(): View
    {
        $items = Item::with('category')
            ->when($this->search !== '', function ($q) {
                $q->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('code', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->category !== '', fn ($q) => $q->where('category_id', $this->category))
            ->when($this->condition !== '', fn ($q) => $q->where('condition', $this->condition))
            ->when($this->availability === 'tersedia', fn ($q) => $q->where('stock', '>', 0))
            ->when($this->availability === 'habis', fn ($q) => $q->where('stock', '<=', 0))
            ->orderBy('name')
            ->paginate(9);

        return view('livewire.item-catalog', [
            'items' => $items,
            'categories' => Category::orderBy('name')->get(),
            'conditions' => Item::CONDITIONS,
        ]);
    }
}
