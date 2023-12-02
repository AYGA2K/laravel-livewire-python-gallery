<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadImage extends Component
{
    use WithFileUploads;
    public $images = [];
    public $categories ;
    public $category;
    public $successMessage;
    public $errorMessage;

    public function render()
    {
        return view('livewire.upload-image');
    }

    public function mount() {
        $this->categories = User::find(auth()->user()->id)->images->pluck('category')->toArray();
    }

    public function store()
    {

        $this->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:20048',             'category' => 'required',
        ]);

        foreach ($this->images as $key => $image) {
            if ($image->isValid()) {
                // Store the image
                $imageName = $image->store('images', 'public');

                // Find the user and create a new image related to the user
                $user = User::find(auth()->user()->id);
                $user->images()->create([
                    'name' => $imageName,
                    'category' => $this->categories[$key]
                ]);
            } else {
                $this->errorMessage = 'Error uploading one or more images.';
                return;
            }
        }

        $this->successMessage = 'Images uploaded successfully.';
    }
}