<?php

namespace App\Livewire\Role;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public bool $ready = false;
    public string $search = '';
    public string $role = '';
    public string $sortField = 'roles.created_at';
    public string $sortDirection = 'asc';
    public int $perPage = 10;
    public int $page = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'role' => ['except' => ''],
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
        $role = Role::findOrFail($id);
        $role->delete();

        session()->flash('message', 'Role deleted successfully.');
    }

    #[On('perform-delete')]
    public function performDelete(int $id): void
    {
        if (!Auth::user()->can('role delete')) {
            $this->dispatch('toast', type: 'error', message: 'Anda tidak punya izin untuk menghapus data.');
            return;
        }

        $this->delete($id);
        $this->dispatch('toast', type: 'success', message: 'Role deleted successfully.');
    }

    protected function currentPageEmpty(): bool
    {
        $count = $this->query()->count();
        $from = ($this->getPage() - 1) * $this->perPage + 1;
        return $count < $from;
    }

    protected function query()
    {
        return Role::query()
            ->select(['roles.id', 'roles.name', 'roles.guard_name', 'roles.created_at'])
            ->when($this->search !== '', function ($q) {
                $s = mb_strtolower($this->search);
                $q->where(function ($qq) use ($s) {
                    $qq->whereRaw('LOWER(roles.name)  LIKE ?', ["%{$s}%"])
                        ->orWhereRaw('LOWER(roles.guard_name) LIKE ?', ["%{$s}%"]);
                });
            })
            ->when($this->role !== '', fn($q) => $q->role($this->role))
            ->orderBy($this->sortField, $this->sortDirection);
    }


    public function render()
    {
        $roles = $this->ready
            ? $this->query()->paginate($this->perPage)
            : new LengthAwarePaginator([], 0, $this->perPage);

        return view('livewire.role.index', [
            'roles' => $roles,
            'page_subtitle' => 'Role List',
        ])->layout('layouts.app', [
            'title' => 'Role | e-RISPK',
            'page_title' => 'Role',
            'page' => 'roles',
        ]);
    }
}
