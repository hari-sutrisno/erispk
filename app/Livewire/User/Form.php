<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class Form extends Component
{
    public ?int $id = null;
    public string $name = '';
    public string $email = '';
    public array $roles = [];
    public ?string $password = null;

    public function mount($id = null): void
    {
        if ($id) {
            $user = User::findOrFail($id);

            $this->id = $user->id;
            $this->name   = $user->name;
            $this->email  = $user->email;
            $this->roles  = $user->roles->pluck('name')->toArray();
        }
    }

    public function save(): void
    {
        $this->validate([
            'name'     => 'required|string',
            'email'    => 'email|unique:users,email,' . ($this->id ?? 'NULL'),
            'password' => $this->id ? 'nullable|min:6' : 'required|min:6',
            'roles'    => 'array',
        ]);

        $user = User::updateOrCreate(
            ['id' => $this->id],
            [
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => $this->id && empty($this->password)
                    ? User::find($this->id)->password
                    : Hash::make($this->password),
            ]
        );

        // Sync roles
        $user->syncRoles($this->roles ?? []);

        session()->flash(
            'message',
            $this->id
                ? 'User updated successfully.'
                : 'User created successfully.'
        );

        redirect()->route('users.index');
    }

    public function render()
    {
        $roleOptions = Role::query()->orderBy('name')->pluck('name');

        return view('livewire.user.form', compact('roleOptions'))
            ->layout('layouts.app', [
                'title'         => $this->id ? 'Edit User | e-RISPK' : 'Create User | e-RISPK',
                'page_title'    => $this->id ? 'Edit User' : 'Create User',
                'page'          => 'users',
            ]);
    }
}
