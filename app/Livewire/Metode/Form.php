<?php

namespace App\Livewire\Metode;

use Livewire\Component;
use App\Models\Metode;

class Form extends Component
{
    public ?int $id = null;
    public $nama;
    public bool $status = true;

    public function mount($id = null): void
    {
        if ($id) {
            $metode = Metode::findOrFail($id);

            $this->id = $metode->id;
            $this->nama = $metode->nama;
            $this->status = $metode->status;
        }
    }

    public function save()
    {
        $this->validate([
            'nama' => 'required|string|min:2|max:120',
            'status' => 'boolean',
        ]);

        $data = [
            'nama'  => $this->nama,
            'status' => $this->status,
        ];

        Metode::updateOrCreate(
            ['id' => $this->id],
            $data
        );

        session()->flash('message', $this->id ? 'Metode cara Ukur updated successfully.' : 'Metode Cara Ukur created successfully.');
        return redirect()->route('metode.index');
    }

    public function render()
    {
        return view('livewire.metode.form', [
            'page_title' => 'Metode',
        ])
            ->layout('layouts.app', [
                'title'         => $this->id ? 'Edit Metode Cara Ukur | e-RISPK' : 'Create Metode Cara Ukur | e-RISPK',
                'page_title'    => 'Metode Cara Ukur',
                'page_subtitle' => $this->id ? 'Edit' : 'Create',
                'page'          => 'metode',
            ]);
    }
}
