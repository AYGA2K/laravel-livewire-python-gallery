<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Obj extends Component
{
    use WithFileUploads;

    public $objFile;
    public $successMessage;
    public $errorMessage;

    public function render()
    {
        return view('livewire.obj')->layout('layouts.app');
    }

    public function store()
    {
        /* $this->validate([ */
        /*     'objFile' => 'required|file|mimes:obj|max:10240', */
        /* ]); */

        try {
            $objFileName = $this->objFile->getClientOriginalName();
            $this->objFile->storeAs('obj_files', $objFileName, 'public');


            $this->successMessage = 'Upload successful.';

            $this->reset(['objFile']);
        } catch (\Exception $e) {
            $this->errorMessage = 'Upload failed. ' . $e->getMessage();
        }
    }
}
