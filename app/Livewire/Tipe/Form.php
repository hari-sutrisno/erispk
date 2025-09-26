<?php

namespace App\Livewire\Tipe;

use Livewire\Component;
use App\Models\Tipe;

class Form extends Component
{
    public ?int $id = null;
    public $nama  = '';

    public function mount($id = null): void
    {
        if ($id) {
            $tipe = Tipe::findOrFail($id);
            $this->id     = $tipe->id;
            $this->nama   = $tipe->nama;
        }
    }

    protected function rules(): array
    {
        return [
            'nama' => 'required|string|min:2|max:200',
        ];
    }

    public function save()
    {
        $data = $this->validate();

        $model = Tipe::updateOrCreate(
            ['id' => $this->id],
            $data
        );

        $this->id = $model->id;

        session()->flash('message', $this->id ? 'Tipe updated successfully.' : 'Tipe created successfully.');
        return redirect()->route('tipe.index');
    }

    public function render()
    {
        return view('livewire.tipe.form', [
            'page_title' => 'Tipe Rencana Aksi',
        ])->layout('layouts.app', [
            'title'         => $this->id ? 'Edit Tipe Rencana Aksi | e-RISPK' : 'Create Tipe Rencana Aksi | e-RISPK',
            'page_title'    => 'Tipe Rencana Aksi',
            'page_subtitle' => $this->id ? 'Edit' : 'Create',
            'page'          => 'tipe',
        ]);
    }
}
