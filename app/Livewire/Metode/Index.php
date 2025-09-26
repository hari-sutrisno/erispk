<?php

namespace App\Livewire\Metode;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use App\Models\Metode;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public bool $ready = false;
    public string $search = '';
    public string $status = '';
    public string $sortField = 'tblm_metode_caraukur.created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;
    public int $page = 1;

    protected $queryString = [
        'search'        => ['except' => ''],
        'status'        => ['except' => ''],
        'sortField'     => ['except' => 'tblm_metode_caraukur.created_at'],
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
        $map = [
            'nama'       => 'tblm_metode_caraukur.nama',
            'status'     => 'tblm_metode_caraukur.status',
            'created_at' => 'tblm_metode_caraukur.created_at',
        ];

        $column = $map[$field] ?? 'tblm_metode_caraukur.created_at';

        if ($this->sortField === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function delete(int $id): void
    {
        $metode = Metode::findOrFail($id);
        $metode->delete();
        session()->flash('message', 'Metode deleted successfully.');
    }

    #[On('perform-delete')]
    public function performDelete(int $id): void
    {
        if (!Auth::user()->can('metode delete')) {
            $this->dispatch('toast', type: 'error', message: 'Anda tidak punya izin untuk menghapus data.');
            return;
        }
        $this->delete($id);
        $this->dispatch('toast', type: 'success', message: 'Metode deleted successfully.');
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
        $sortable = ['tblm_metode_caraukur.created_at', 'tblm_metode_caraukur.nama', 'tblm_metode_caraukur.status'];
        if (!in_array($this->sortField, $sortable, true)) {
            $this->sortField = 'tblm_metode_caraukur.created_at';
        }
        $dir = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        return Metode::query()
            ->select([
                'tblm_metode_caraukur.id',
                'tblm_metode_caraukur.nama',
                'tblm_metode_caraukur.status',
                'tblm_metode_caraukur.created_at',
            ])
            ->when($this->search !== '', function ($q) {
                $s = '%' . mb_strtolower($this->search) . '%';
                $q->where(function ($qq) use ($s) {
                    $qq->whereRaw('LOWER(tblm_metode_caraukur.nama) LIKE ?', [$s]);
                });
            })
            // filter status jika dipilih
            ->when($this->status !== '' && $this->status !== null, function ($q) {
                $q->where('tblm_metode_caraukur.status', (int) $this->status); // 1/0
            })
            ->orderBy($this->sortField, $dir);
    }

    public function render()
    {
        $statusOptions = [1 => 'Aktif', 0 => 'Nonaktif'];

        $metode = $this->ready
            ? $this->query()->paginate($this->perPage)
            : new LengthAwarePaginator([], 0, $this->perPage, $this->getPage());

        return view('livewire.metode.index', [
            'metodes'    => $metode,
            'statusOptions' => $statusOptions,
            'page_subtitle' => 'Metode Cara Ukur List',
            'page'          => 'metode',
        ])->layout('layouts.app', [
            'title'      => 'Metode Cara Ukur | e-RISPK',
            'page_title' => 'Metode',
            'page'       => 'metode',
        ]);
    }
}
