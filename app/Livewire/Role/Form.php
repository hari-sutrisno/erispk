<?php

namespace App\Livewire\Role;

use Livewire\Component;
use App\Models\Role;
use App\Models\Permission;

class Form extends Component
{
    public $roleId;
    public $name = '';
    public $permissions = [];
    public $selectedPermissions = [];
    public $groupedPermissions = [];

    public function mount($role = null)
    {
        $permissions = Permission::all();

        $this->groupedPermissions = $permissions->groupBy(function ($perm) {
            return explode(' ', $perm->name)[0];
        })->map(function ($group) {
            return $group->pluck('name')->values();
        });

        if ($role) {
            $this->roleId = $role;
            $r = Role::with('permissions')->findOrFail($role);
            $this->name = $r->name;
            $this->selectedPermissions = $r->permissions->pluck('name')->toArray();
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|unique:roles,name,' . $this->roleId,
        ]);

        $role = $this->roleId
            ? Role::findOrFail($this->roleId)->update(['name' => $this->name])
            : Role::create(['name' => $this->name]);

        $role = Role::where('name', $this->name)->first();

        $oldPermissions = $role->permissions->pluck('name')->toArray();

        $role->syncPermissions($this->selectedPermissions);

        $newPermissions = $role->permissions->pluck('name')->toArray();

        // Tambahkan log manual
        if ($oldPermissions !== $newPermissions) {
            activity('role')
                ->causedBy(auth()->user())
                ->setEvent('updated')
                ->performedOn($role)
                ->withProperties([
                    'old' => $oldPermissions,
                    'new' => $newPermissions,
                ])
                ->log('Updated role permissions');
        }

        session()->flash('message', 'Role berhasil disimpan.');
        return redirect()->route('roles.index');
    }

    public function render()
    {
        return view('livewire.role.form', [
            'page_title' => 'Role',
        ])
            ->layout('layouts.app', [
                'title'      => $this->roleId ? 'Edit Role | e-RISPK' : 'Create Role | e-RISPK',
                'page_title' => $this->roleId ? 'Edit Role' : 'Create Role',
                'page'       => 'roles',
                'title' => 'Role | e-RISPK',
                'page_title' => 'Role',
            ]);
    }
}
