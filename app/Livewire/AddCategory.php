<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
class AddCategory extends Component
{
    public $categoryName;
    public $successMessage;
    public $errorMessage;
    public function store()
    {
        $this->validate([
            'categoryName' => 'required|unique:categories,name',
        ]);

        Category::create(['name' => $this->categoryName]);

        $this->resetForm();
        $this->successMessage = 'Category added successfully.';

    }

    public function render()
    {
        return view('livewire.add-category');
    }

    private function resetForm()
    {
        $this->categoryName = null;
    }

}
