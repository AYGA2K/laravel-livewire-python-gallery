<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Image;
use Illuminate\Support\Facades\Http;

class ProcessingImage extends Component
{
    public $imageId;
    public $picture;
    public $showCropForm = false;
    public $imageInfo, $image;
    public $similar_images;
    public  $dataB, $dataR, $dataG, $roughness, $contrast, $linelikeness,  $coarseness, $regularity;
    public $histogram_data, $gabor_data, $trauma_data, $color_moment_data, $directionality;
    public $ClusteringData;
    public function cropImage($imageId)
    {
        // redirect to the image process page
        return redirect('/crop/' . $imageId);
    }
  public function getClusteringByRGBcolors($imageName){
      $response = Http::get("127.0.0.1:5000/getClusteringByRGBcolors?imageName=" . $imageName);
      $this->ClusteringData = $response->json();
  }


    public function mount($imageId)
    {
        $this->image = Image::find($imageId);
        $response = Http::get("127.0.0.1:5000/getSimilarImages?imageName=" . $this->image->name);
        $data = $response->json();
        $this->similar_images = array_keys($data);

        $this->imageId = $imageId;
        $this->dataR = json_decode($this->image->HistoR);
        $this->dataB = json_decode($this->image->HistoB);
        $this->dataG = json_decode($this->image->HistoG);

        $this->gabor_data = json_decode($this->image->Gabor);
        $this->trauma_data = json_decode(json_decode($this->image->Trauma));
        $this->contrast = $this->trauma_data->contrast;
        $this->directionality = $this->trauma_data->directionality;
        $this->coarseness = $this->trauma_data->coarseness;
        $this->linelikeness = $this->trauma_data->linelikeness;
        $this->regularity = $this->trauma_data->regularity;
        $this->roughness = $this->trauma_data->roughness;
        $this->color_moment_data = json_decode($this->image->ColorM);
        $this->getClusteringByRGBcolors($this->image->name);
    }
    public function render()
    {
        $this->picture = \App\Models\Image::find($this->imageId);
        return view(
            'livewire.process-image'
        )->layout('layouts.app');
    }
}
