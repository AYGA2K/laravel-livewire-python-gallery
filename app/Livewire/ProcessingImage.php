<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Image;
use Illuminate\Support\Facades\Http;

class ProcessingImage extends Component
{
    public $imageId;
    public $picture;

    public $imageInfo, $image;
    public $similar_images;
    public  $dataB, $dataR, $dataG, $roughness, $contrast, $linelikeness,  $coarseness, $regularity;
    public $histogram_data , $gabor_data, $trauma_data, $color_moment_data ,$directionality ;
    public $cropX , $cropY  , $cropWidth , $cropHeight ;

    public function crop() {
        //dd([$this->cropX , $this->cropY  , $this->cropWidth , $this->cropHeight ]);
        //Http::get("127.0.0.1:5000/cropImage?imageName=" . $this->image->name . ",x=" . $this->cropX . ",y=" . $this->cropY .",width=" . $this->cropWidth . ",height=". $this->cropHeight)->body();
        Http::get("127.0.0.1:5000/getSimilarImages?imageName=" . $this->image->name )->body();
    }

    public function mount($imageId)
    {
        $this->similar_images = Image::where('user_id', auth()->user()->id)->get();
        $this->imageId = $imageId;
        $this->image = Image::find($imageId);
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
        $this->roughness= $this->trauma_data->roughness;
        $this->color_moment_data = json_decode($this->image->ColorM) ;
    }
    public function render()
    {
        $this->picture = \App\Models\Image::find($this->imageId);
        return view(
            'livewire.process-image'
        )->layout('layouts.app');
    }

}
