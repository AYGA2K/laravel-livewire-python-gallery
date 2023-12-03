<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;


class UploadImage extends Component
{
    use WithFileUploads;
    public $images = [];
    public $categories;
    public $category;
    public $successMessage;
    public $errorMessage;

    public function render()
    {
        return view('livewire.upload-image');
    }

    public function mount()
    {
        $this->categories = Category::where('user_id', auth()->user()->id)->pluck('name');

        if ($this->categories->count() > 0) {
            $this->category = $this->categories[0];
        }
    }

    public function store()
    {

        $this->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:20048', 'category' => 'required',
        ]);

        foreach ($this->images as $key => $image) {
            if ($image->isValid()) {
                // Store the image
                $imageName = $image->store('images', 'public');


                // Find the user and create a new image related to the user
                $user = User::find(auth()->user()->id);
                $category = Category::firstWhere('name', $this->category);
                $imageModel = Image::create([
                    'name' => $imageName,
                ]);
                $user->images()->save($imageModel);
                $category->images()->save($imageModel);
                $imageModel->save();


                // calling Agent's 47 fucking api
                // this block
                try {
                    Http::get("127.0.0.1:5000/preprocessing?imageName=" . $imageName);
                    $this->errorMessage = "" ;
                }
                catch (ConnectionException $e) {
                    $this->errorMessage = "Cant's connect to the server " . $e->getMessage() ;
                }
            } else {
                $this->errorMessage = 'Error uploading one or more images.';
                return;
            }
        }

        $this->successMessage = 'Images uploaded successfully.';
    }
}
