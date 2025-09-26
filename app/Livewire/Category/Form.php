<?php

namespace App\Livewire\Category;

use Livewire\Component;
use App\Models\Category;

class Form extends Component
{
    public ?int $id = null;
    public $nama;
    public $keterangan;
    public bool $status = true;

    public function mount($id = null): void
    {
        if ($id) {
            $category = Category::findOrFail($id);
            
            $this->id = $category->id;
            $this->nama = $category->nama;
            $this->keterangan = $category->keterangan;
            $this->status = $category->status;
        }
    }

    public function save()
    {
        $this->validate([
            'nama' => 'required|string|min:2|max:120',
            'keterangan' => 'nullable|string|max:1000',
            'status' => 'boolean',
        ]);

        $data = [
            'nama'  => $this->nama,
            'keterangan' => $this->keterangan,
            'status' => $this->status,
        ];

        Category::updateOrCreate(
            ['id' => $this->id],
            $data
        );

        session()->flash('message', $this->id ? 'Category updated successfully.' : 'Category created successfully.');
        return redirect()->route('categories.index');
    }

    public function render()
    {
        return view('livewire.category.form', [
            'page_title' => 'Category',
        ])
            ->layout('layouts.app', [
                'title'         => $this->id ? 'Edit Category | e-RISPK' : 'Create Category | e-RISPK',
                'page_title'    => 'Category',
                'page_subtitle' => $this->id ? 'Edit' : 'Create',
                'page'          => 'categories',
            ]);
    }
}
