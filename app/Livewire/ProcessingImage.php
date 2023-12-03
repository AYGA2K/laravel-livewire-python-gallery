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
    public $similar_images;
    public $histogram_data , $gabor_data, $trauma_data, $color_moment_data, $dataB, $dataR, $dataG, $roughness, $contrast, $linelikeness, $directionality, $coarseness, $regularity;


    public function mount($imageId)
    {
        $this->similar_images = Image::where('user_id', auth()->user()->id)->get();
        $this->imageId = $imageId;
        $image = Image::find($imageId);
        // $this->histogram_data =
        //                   array(
        //                       "RED" => $image->HistoR,
        //                       "BLUE" => $image->HistoB,
        //                       "GREEN" => $image->HistoG
        //                   );
        $this->dataR = json_decode($image->HistoR);
        $this->dataB = json_decode($image->HistoB);
        $this->dataG = json_decode($image->HistoG);

        $this->gabor_data = json_decode($image->Gabor);
        $this->trauma_data = json_decode(json_decode($image->Trauma));
        $this->contrast = $this->trauma_data->contrast;
        $this->directionality = $this->trauma_data->directionality;
        $this->coarseness = $this->trauma_data->coarseness;
        $this->linelikeness = $this->trauma_data->linelikeness;
        $this->regularity = $this->trauma_data->regularity;
        $this->roughness= $this->trauma_data->roughness;

        $this->color_moment_data = json_decode($image->ColorM) ;
    }
    public function render()
    {
        $this->picture = \App\Models\Image::find($this->imageId);
        return view(
            'livewire.process-image'
        )->layout('layouts.app');
    }

    // public function getHistogram(): void
    // {
    //     $this->histogram_clicked = true;
    //     $path = pathinfo($this->picture->name);
    //     $this->histogram_pic_path = Http::get("127.0.0.1:5000/getColorHistogram?imageName=" . $path['basename'])->body();
    //     $this->render();
    // }

}
