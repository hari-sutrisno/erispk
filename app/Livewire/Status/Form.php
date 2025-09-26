<?php

namespace App\Livewire\Status;

use Livewire\Component;
use App\Models\Status;
use Illuminate\Validation\Rule;

class Form extends Component
{
    public ?int $id = null;
    public $status_code = null;
    public $keterangan  = '';

    public function mount($id = null): void
    {
        if ($id) {
            $status = Status::findOrFail($id);
            $this->id           = $status->id;
            $this->status_code  = $status->status_code;
            $this->keterangan   = $status->keterangan;
        }
    }

    protected function rules(): array
    {
        return [
            'status_code' => [
                'required',
                'integer',
                'min:0',
                Rule::unique('tblm_status', 'status_code')->ignore($this->id),
            ],
            'keterangan' => 'required|string|min:2|max:200',
        ];
    }

    protected function messages(): array
    {
        return [
            'status_code.unique' => 'Kode status sudah dipakai.',
        ];
    }

    public function save()
    {
        $data = $this->validate();
        $data['status_code'] = (int) $data['status_code'];

        $model = Status::updateOrCreate(
            ['id' => $this->id],
            $data
        );

        $this->id = $model->id;

        session()->flash('message', $this->id ? 'Status updated successfully.' : 'Status created successfully.');
        return redirect()->route('statuses.index');
    }

    public function render()
    {
        return view('livewire.status.form', [
            'page_title' => 'Status',
        ])->layout('layouts.app', [
            'title'         => $this->id ? 'Edit Status | e-RISPK' : 'Create Status | e-RISPK',
            'page_title'    => 'Status',
            'page_subtitle' => $this->id ? 'Edit' : 'Create',
            'page'          => 'statuses',
        ]);
    }
}
