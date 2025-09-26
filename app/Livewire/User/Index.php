<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    public bool $ready = false;
    public string $search = '';
    public string $role = '';
    public string $sortField = 'users.created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;
    public int $page = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'role' => ['except' => ''],
        'sortField' => ['except' => 'users.created_at'],
        'sortDirection' => ['except' => 'desc'],
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
        if ($id === Auth::id()) {
            session()->flash('message', 'Tidak bisa menghapus akun sendiri.');
            return;
        }

        $user = User::findOrFail($id);
        $user->delete();

        session()->flash('message', 'User deleted successfully.');
    }

    #[On('perform-delete')]
    public function performDelete(int $id): void
    {
        if (!Auth::user()->can('user delete')) {
            $this->dispatch('toast', type: 'error', message: 'Anda tidak punya izin untuk menghapus data.');
            return;
        }

        $this->delete($id);
        $this->dispatch('toast', type: 'success', message: 'User deleted successfully.');
    }

    protected function currentPageEmpty(): bool
    {
        $count = $this->query()->count();
        $from = ($this->getPage() - 1) * $this->perPage + 1;
        return $count < $from;
    }

    protected function query()
    {
        return User::query()
            ->select(['users.id', 'users.name', 'users.email', 'users.created_at'])
            ->with(['roles:id,name'])
            ->when($this->search !== '', function ($q) {
                $s = mb_strtolower($this->search);
                $q->where(function ($qq) use ($s) {
                    $qq->whereRaw('LOWER(users.name)  LIKE ?', ["%{$s}%"])
                        ->orWhereRaw('LOWER(users.email) LIKE ?', ["%{$s}%"]);
                });
            })
            ->when($this->role !== '', fn($q) => $q->role($this->role))
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $roles = Role::query()->orderBy('name')->pluck('name');
        $users = $this->ready
            ? $this->query()->paginate($this->perPage)
            : collect();

        return view('livewire.user.index', [
            'users' => $users,
            'roles' => $roles,
            'page_subtitle' => 'User List',
        ])->layout('layouts.app', [
            'title' => 'User | e-RISPK',
            'page_title' => 'User',
            'page' => 'users',
        ]);
    }
}
