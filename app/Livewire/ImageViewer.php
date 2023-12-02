<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Image;

use Illuminate\Support\Facades\Http;

class ImageViewer extends Component
{
    public $images;
    public $picture = null;
    public $selectedCategory = 'All';
    public $categories;
    public $imageInfo;
    public $histogram_clicked = false, $croping_clicked = false, $clustering_clicked = false, $get_color_clicked;
    public $histogram_pic_path, $cropped_pic_path, $clustered_pic_path, $get_colored_pic_path;
    public $x = 0, $y = 0, $width = 0, $height = 0, $cropID = 0, $k = 2;
    public function loadImagesByCategory()
    {
        $this->imageInfo = null;
        if ($this->selectedCategory === 'All') {
            $this->images = Image::where('user_id', auth()->user()->id)->get();
        } else {
            $this->images = Image::where('category', $this->selectedCategory)->where('user_id', auth()->user()->id)->get();
        }
    }
    public function getImageInfo($imageId)
    {
        // here we get the image info from the python server
        $this->imageInfo = Image::find($imageId);
        $this->picture = Image::find($imageId);
        $this->histogram_pic_path=null;
        $this->cropped_pic_path=null;
        $this->clustered_pic_path=null;
        $this->get_colored_pic_path=null;
        $this->histogram_clicked = false; $this->croping_clicked = false;  $this->clustering_clicked = false; $this->get_color_clicked=false;
        $this->render();
    }


    public function return_if_exists($data, $field)
    {
        if ($data != null) {
            return $data[$field];
        }
        return null;
    }
    public function mount()
    {
        $this->categories = Image::where('user_id', auth()->user()->id)->get('category');
        $this->loadImagesByCategory();
    }
    public function render()
    {
        return view('livewire.image-viewer');
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
