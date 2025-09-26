<?php

namespace App\Livewire\Tipe;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use App\Models\Tipe;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public bool $ready = false;
    public string $search = '';
    public string $sortField = 'tblm_tipe_rencanaaksi.created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;
    public int $page = 1;

    protected $queryString = [
        'search'        => ['except' => ''],
        'sortField'     => ['except' => 'tblm_tipe_rencanaaksi.created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage'       => ['except' => 50],
        'page'          => ['except' => 1],
    ];

    public function bootstrap(): void
    {
        $this->ready = true;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingTipe()
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
            'nama' => 'tblm_tipe_rencanaaksi.nama',
            'created_at' => 'tblm_tipe_rencanaaksi.created_at',
        ];

        $column = $map[$field] ?? 'tblm_tipe_rencanaaksi.created_at';

        if ($this->sortField === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function delete(int $id): void
    {
        $tipe = Tipe::findOrFail($id);
        $tipe->delete();
        session()->flash('message', 'Tipe deleted successfully.');
    }

    #[On('perform-delete')]
    public function performDelete(int $id): void
    {
        if (!Auth::user()->can('tipe delete')) {
            $this->dispatch('toast', type: 'error', message: 'Anda tidak punya izin untuk menghapus data.');
            return;
        }
        $this->delete($id);
        $this->dispatch('toast', type: 'success', message: 'Tipe deleted successfully.');
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
        $sortable = ['tblm_tipe_rencanaaksi.created_at', 'tblm_tipe_rencanaaksi.nama'];
        if (!in_array($this->sortField, $sortable, true)) {
            $this->sortField = 'tblm_tipe_rencanaaksi.created_at';
        }
        $dir = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        return Tipe::query()
            ->select([
                'tblm_tipe_rencanaaksi.id',
                'tblm_tipe_rencanaaksi.nama',
                'tblm_tipe_rencanaaksi.created_at',
            ])
            ->when($this->search !== '', function ($q) {
                $s = '%' . mb_strtolower($this->search) . '%';
                $q->where(function ($qq) use ($s) {
                    $qq->whereRaw('LOWER(tblm_tipe_rencanaaksi.nama) LIKE ?', [$s]);
                });
            })
            ->orderBy($this->sortField, $dir);
    }

    public function render()
    {
        $tipe = $this->ready
            ? $this->query()->paginate($this->perPage)
            : new LengthAwarePaginator([], 0, $this->perPage, $this->getPage());

        return view('livewire.tipe.index', [
            'types'         => $tipe,
            'page_subtitle' => 'Tipe Rencana Aksi List',
            'page'          => 'tipe',
        ])->layout('layouts.app', [
            'title'      => 'Tipe Rencana Aksi | e-RISPK',
            'page_title' => 'Tipe Rencana Aksi',
            'page'       => 'tipe',
        ]);
    }
}
