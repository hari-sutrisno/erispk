<?php

namespace App\Livewire\Permission;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public bool $ready = false;
    public string $search = '';
    public string $permission = '';
    public string $sortField = 'permissions.created_at';
    public string $sortDirection = 'asc';
    public int $perPage = 50;
    public int $page = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'permission' => ['except' => ''],
        'sortField' => ['except' => 'users.created_at'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
        'page' => ['except' => 1],
    ];

    public function bootstrap(): void
    {
        $this->ready = true;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRole()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function delete(int $id): void
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        session()->flash('message', 'Permission deleted successfully.');
    }

    #[On('perform-delete')]
    public function performDelete(int $id): void
    {
        if (!Auth::user()->can('permission delete')) {
            $this->dispatch('toast', type: 'error', message: 'Anda tidak punya izin untuk menghapus data.');
            return;
        }

        $this->delete($id);
        $this->dispatch('toast', type: 'success', message: 'Permission deleted successfully.');
    }

    protected function currentPageEmpty(): bool
    {
        $count = $this->query()->count();
        $from = ($this->getPage() - 1) * $this->perPage + 1;
        return $count < $from;
    }

    protected function query()
    {
        return Permission::query()
            ->select(['permissions.id', 'permissions.name', 'permissions.guard_name', 'permissions.created_at'])
            ->when($this->search !== '', function ($q) {
                $s = mb_strtolower($this->search);
                $q->where(function ($qq) use ($s) {
                    $qq->whereRaw('LOWER(permissions.name)  LIKE ?', ["%{$s}%"])
                        ->orWhereRaw('LOWER(permissions.guard_name) LIKE ?', ["%{$s}%"]);
                });
            })
            ->when($this->permission !== '', fn($q) => $q->permission($this->permission))
            ->orderBy($this->sortField, $this->sortDirection);
    }


    public function render()
    {
        $permissions = $this->ready
            ? $this->query()->paginate($this->perPage)
            : new LengthAwarePaginator([], 0, $this->perPage);

        return view('livewire.permission.index', [
            'permissions' => $permissions,
            'page_subtitle' => 'Permission List',
            'page' => 'permissions',
        ])->layout('layouts.app', [
            'title' => 'Permission | e-RISPK',
            'page_title' => 'Permission',
            'page' => 'permissions',
        ]);
    }
}
