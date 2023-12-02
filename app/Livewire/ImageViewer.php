<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Image;

use App\Models\Category;

class ImageViewer extends Component
{
    public $images;
    public $picture = null;
    public $selectedCategory = 'All';
    public $categories;
    public function loadImagesByCategory()
    {
        if ($this->selectedCategory === 'All') {
            $this->images = Image::where('user_id', auth()->user()->id)->get();
        } else {
            $category = Category::firstWhere('name', $this->selectedCategory);
            $this->images = Image::where('user_id', auth()->user()->id)->where('category_id', $category->id)->get();
        }
    }

    public function processImage($imageId)
    {
        // redirect to the image process page
        return redirect('/process/' . $imageId);
    }

    public function mount()
    {
        $this->categories = ['All'];
        $dataCategories = Category::all()->pluck('name');
        $this->categories = array_merge($this->categories, $dataCategories->toArray());

        $this->loadImagesByCategory();
    }
    public function render()
    {
        return view('livewire.image-viewer');
    }
}
