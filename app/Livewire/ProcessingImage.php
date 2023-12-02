<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Image;
use Illuminate\Support\Facades\Http;

class ProcessingImage extends Component
{
    public $imageId;
    public $picture;

    public $imageInfo;
    public $histogram_clicked = false, $croping_clicked = false, $clustering_clicked = false, $get_color_clicked;
    public $histogram_pic_path, $cropped_pic_path, $clustered_pic_path, $get_colored_pic_path;
    public $x = 0, $y = 0, $width = 0, $height = 0, $cropID = 0, $k = 2;
    public function mount($imageId)
    {
        $this->imageId = $imageId;
    }
    public function render()
    {
        $this->picture = \App\Models\Image::find($this->imageId);
        return view(
            'livewire.process-image'
        )->layout('layouts.app');
    }

    public function getHistogram(): void
    {
        $this->histogram_clicked = true;
        $path = pathinfo($this->picture->name);
        $this->histogram_pic_path = Http::get("127.0.0.1:5000/getColorHistogram?imageName=" . $path['basename'])->body();
        $this->render();
    }

    public function cropPic()
    {
        $this->croping_clicked = true;
        $path = pathinfo($this->picture->name);
        $this->cropped_pic_path = Http::get("127.0.0.1:5000/crop?imageName=" . $path['basename'] . "&x=" . $this->x . "&y=" . $this->y . "&width=" . $this->width . "&height=" . $this->height)->body();
        $this->render();
    }


    public function clustering()
    {
        $this->clustering_clicked = true;
        $path = pathinfo($this->picture->name);
        $this->clustered_pic_path = Http::get("127.0.0.1:5000/clusteringByColor?imageName=" . $path['basename'] . "&k=" . $this->k)->body();
        $this->render();
    }

    public function getColorMoment(): void
    {
        $this->get_color_clicked = true;
        $path = pathinfo($this->picture->name);
        $this->get_colored_pic_path = Http::get("127.0.0.1:5000/getColorMoment?imageName=" . $path['basename'])->body();
        $this->render();
    }
}
