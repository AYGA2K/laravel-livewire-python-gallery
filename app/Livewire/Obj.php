<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Http;

class Obj extends Component
{
    use WithFileUploads;

    public $objFile;
    public $successMessage;
    public $errorMessage;
    public $images = [];
    public $isProcessing = true;

    public function render()
    {
        return view('livewire.obj')->layout('layouts.app');
    }

    public function store()
    {

        try {
            $objFileName = $this->objFile->getClientOriginalName();
            $this->objFile->storeAs('obj_files', $objFileName, 'public');

            $this->successMessage = 'Upload successful.';
            $response = Http::get('http://127.0.0.1:5000/getSimilarObjs', ['name' => $objFileName]);
            $this->images = $response->json();
            $this->isProcessing = false;
            $this->reset(['objFile']);
        } catch (\Exception $e) {
            $this->errorMessage = 'Upload failed. ' . $e->getMessage();
        }
    }
}
