<?php

namespace App\Livewire\Category;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public bool $ready = false;
    public string $search = '';
    public string $status = '';
    public string $sortField = 'tblm_kategori.created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;
    public int $page = 1;

    protected $queryString = [
        'search'        => ['except' => ''],
        'status'        => ['except' => ''],
        'sortField'     => ['except' => 'tblm_kategori.created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage'       => ['except' => 10],
        'page'          => ['except' => 1],
    ];

    /** dipanggil via wire:init="bootstrap" di view */
    public function bootstrap(): void
    {
        $this->ready = true;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingStatus()
    {
        $this->resetPage();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        // izinkan alias sederhana dari UI
        $map = [
            'nama'       => 'tblm_kategori.nama',
            'status'     => 'tblm_kategori.status',
            'created_at' => 'tblm_kategori.created_at',
        ];

        $column = $map[$field] ?? 'tblm_kategori.created_at';

        if ($this->sortField === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function delete(int $id): void
    {
        $category = Category::findOrFail($id);
        $category->delete();
        session()->flash('message', 'Category deleted successfully.');
    }

    #[On('perform-delete')]
    public function performDelete(int $id): void
    {
        if (!Auth::user()->can('category delete')) {
            $this->dispatch('toast', type: 'error', message: 'Anda tidak punya izin untuk menghapus data.');
            return;
        }
        $this->delete($id);
        $this->dispatch('toast', type: 'success', message: 'Category deleted successfully.');
    }

    protected function currentPageEmpty(): bool
    {
        $count = $this->query()->count();
        $from  = ($this->getPage() - 1) * $this->perPage + 1;
        return $count < $from;
    }

    protected function query()
    {
        // safety: hanya izinkan sort kolom berikut
        $sortable = ['tblm_kategori.created_at', 'tblm_kategori.nama', 'tblm_kategori.status'];
        if (!in_array($this->sortField, $sortable, true)) {
            $this->sortField = 'tblm_kategori.created_at';
        }
        $dir = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        return Category::query()
            ->select([
                'tblm_kategori.id',
                'tblm_kategori.nama',
                'tblm_kategori.keterangan',
                'tblm_kategori.status',
                'tblm_kategori.created_at',
            ])
            ->when($this->search !== '', function ($q) {
                $s = '%' . mb_strtolower($this->search) . '%';
                $q->where(function ($qq) use ($s) {
                    $qq->whereRaw('LOWER(tblm_kategori.nama) LIKE ?', [$s])
                        ->orWhereRaw('LOWER(tblm_kategori.keterangan) LIKE ?', [$s]);
                });
            })
            // filter status jika dipilih
            ->when($this->status !== '' && $this->status !== null, function ($q) {
                $q->where('tblm_kategori.status', (int) $this->status); // 1/0
            })
            ->orderBy($this->sortField, $dir);
    }

    public function render()
    {
        $statusOptions = [1 => 'Aktif', 0 => 'Nonaktif'];

        $categories = $this->ready
            ? $this->query()->paginate($this->perPage)
            : new LengthAwarePaginator([], 0, $this->perPage, $this->getPage());

        return view('livewire.category.index', [
            'categories'    => $categories,
            'statusOptions' => $statusOptions,
            'page_subtitle' => 'Category List',
            'page'          => 'categories',
        ])->layout('layouts.app', [
            'title'      => 'Category | e-RISPK',
            'page_title' => 'Category',
            'page'       => 'categories',
        ]);
    }
}
