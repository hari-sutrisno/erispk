<?php

namespace App\Livewire\Permission;

use Livewire\Component;
use App\Models\Permission;

class Form extends Component
{
    public $id;
    public $name = '';

    public function mount($permission = null)
    {
        if ($permission) {
            $p = Permission::findOrFail($permission);
            $this->id = $p->id;
            $this->name = $p->name;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|unique:permissions,name,' . $this->id,
        ]);

        if ($this->id) {
            Permission::findOrFail($this->id)->update(['name' => $this->name]);
        } else {
            Permission::create(['name' => $this->name]);
        }

        session()->flash('message', 'Permission berhasil disimpan.');
        return redirect()->route('permissions.index');
    }

    public function render()
    {
        return view('livewire.permission.form', [
            'page_title' => 'Permission',
        ])
            ->layout('layouts.app', [
                'title'         => $this->id ? 'Edit Permission | e-RISPK' : 'Create Permission | e-RISPK',
                'page_title'    => 'Permission',
                'page_subtitle' => $this->id ? 'Edit' : 'Create',
                'page'          => 'permissions',
            ]);
    }
}
