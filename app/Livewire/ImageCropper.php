<?php

namespace App\Livewire;

use Livewire\Component;

class ImageCropper extends Component
{
    public $cropX, $cropY, $cropWidth, $cropHeight;

    public $imageId;
    public $picture;
    protected $listeners = ['updateCropData'];

    public function updateCropData($data)
    {
        $this->cropX = $data['x'];
        $this->cropY = $data['y'];
        $this->cropWidth = $data['width'];
        $this->cropHeight = $data['height'];
    }

    public function crop()
    {
    }
    public function mount($imageId)
    {
        $this->imageId = $imageId;
    }

    public function render()
    {
        $this->picture = \App\Models\Image::find($this->imageId);
        return view('livewire.image-cropper')->layout('layouts.app');
    }
}
