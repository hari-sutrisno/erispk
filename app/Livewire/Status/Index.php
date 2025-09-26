<?php

namespace App\Livewire\Status;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public bool $ready = false;
    public string $search = '';
    public string $sortField = 'tblm_status.created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;
    public int $page = 1;

    protected $queryString = [
        'search'        => ['except' => ''],
        'sortField'     => ['except' => 'tblm_status.created_at'],
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
            'status_code' => 'tblm_status.status_code',
            'keterangan' => 'tblm_status.keterangan',
            'created_at' => 'tblm_status.created_at',
        ];

        $column = $map[$field] ?? 'tblm_status.created_at';

        if ($this->sortField === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function delete(int $id): void
    {
        $status = Status::findOrFail($id);
        $status->delete();
        session()->flash('message', 'Status deleted successfully.');
    }

    #[On('perform-delete')]
    public function performDelete(int $id): void
    {
        if (!Auth::user()->can('status delete')) {
            $this->dispatch('toast', type: 'error', message: 'Anda tidak punya izin untuk menghapus data.');
            return;
        }
        $this->delete($id);
        $this->dispatch('toast', type: 'success', message: 'Status deleted successfully.');
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
        $sortable = ['tblm_status.created_at', 'tblm_status.status_code', 'tblm_status.keterangan'];
        if (!in_array($this->sortField, $sortable, true)) {
            $this->sortField = 'tblm_status.created_at';
        }
        $dir = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        return Status::query()
            ->select([
                'tblm_status.id',
                'tblm_status.status_code',
                'tblm_status.keterangan',
                'tblm_status.created_at',
            ])
            ->when($this->search !== '', function ($q) {
                $s = '%' . mb_strtolower($this->search) . '%';
                $q->where(function ($qq) use ($s) {
                    $qq->whereRaw('LOWER(tblm_status.keterangan) LIKE ?', [$s]);
                });
            })
            ->orderBy($this->sortField, $dir);
    }

    public function render()
    {
        $statuses = $this->ready
            ? $this->query()->paginate($this->perPage)
            : new LengthAwarePaginator([], 0, $this->perPage, $this->getPage());

        return view('livewire.status.index', [
            'statuses'    => $statuses,
            'page_subtitle' => 'Status List',
            'page'          => 'statuses',
        ])->layout('layouts.app', [
            'title'      => 'Status | e-RISPK',
            'page_title' => 'Status',
            'page'       => 'statuses',
        ]);
    }
}
