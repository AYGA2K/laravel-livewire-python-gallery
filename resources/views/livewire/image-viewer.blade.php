<div>
    <div class="mb-4">
        <select wire:change="loadImagesByCategory" name="selectedCategory" wire:model="selectedCategory" id="selectedCategory" class="text-black mt-1 p-2 border rounded-md w-full @error('category') border-red-500 @enderror">
            <option value="" disabled selected>Select a category</option>
            @foreach ($categories as $cat)
            <option value="{{ $cat }}"> {{$cat}} </option>
            @endforeach
        </select>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach ($images as $image)

        <div wire:click="getImageInfo({{$image->id}})" class=" p-4 cursor-pointer">
            <img src="{{ asset('storage/' . $image->name) }}" alt="{{ $image->name }}" class="w-full h-auto">
        </div>

        @endforeach
    </div>



    <h3 class="mt-12">Set of Tools</h3>

    <div class="flex mt-12 space-x-12">
        <div class="w-1/2">
            @if ($picture != null)
            <img src="{{ asset('storage/' . App\Livewire\ImageViewer::return_if_exists($picture, "name")) }}" class="w-full h-auto">
            @else
            <h3>Please select an image from the uploaded files above.</h3>
            @endif
        </div>
        <div>
            <h4>Histogram</h4>
            <form wire:submit="getHistogram" class="mt-4 p-4">
                <button type="submit" class="w-32 border-3 bg-cyan-500 py-2 hover:bg-cyan-700  border-black rounded-lg">Get Histogram</button>
            </form>
            <hr class="my-4">
            <h4 class="my-4">Cropping</h4>
            <form wire:submit="cropPic" class="mt-4 p-4   border-3 border-black rounded-lg">
                <label for="width">Width</label>
                <input type="number" wire:model="width" id="width" class="border-2 text-black border-black rounded-md p-1 w-32">
                <label for="height">Height</label>
                <input type="number" wire:model="height" id="height" class="border-2 text-black border-black rounded-md p-1 w-32">
                </br>
                <label for="x">X</label>
                <input type="number" wire:model="x" id="x" class="border-2 border-black text-black rounded-md p-1 w-32">
                <label for="y">Y</label>
                <input type="number" wire:model="y" id="y" class="border-2 border-black text-black rounded-md p-1 w-32">
              
                <button type="submit" class="w-32 border-3 border-black rounded-lg mt-4 bg-cyan-500 py-2 hover:bg-cyan-700  ">Crop</button>
            </form>
            <hr class="my-4">
            <h4 class="my-4">Clustering</h4>
            <form wire:submit="clustering" class="mt-4 p-4 border-3 border-black rounded-lg">
                <label for="k">Number of Clusters</label>
                <input type="number" wire:model="k" id="k" class="border-2 border-black text-black rounded-md p-1 w-32">
                <button type="submit" class="w-32 border-3 border-black rounded-lg mt-4 bg-cyan-500 py-2 hover:bg-cyan-700 ">Cluster</button>
            </form>
            <hr class="my-4">
            <h4 class="my-4">Get Color Moment</h4>
            <form wire:submit="getColorMoment" class="mt-4 p-4 border-3 border-black rounded-lg">
                <button type="submit" class="w-32 border-3 border-black rounded-lg mt-4 bg-cyan-500 py-2 hover:bg-cyan-700  ">Get Color Moment</button>
            </form>

            @if ($picture != null && $histogram_clicked == true)
            <h4 class="my-4">Histogram</h4>
            <img class="my-4" src="{{ asset('storage/images/' . $histogram_pic_path) }}">
            @endif

            @if ($picture != null && $croping_clicked == true)
            <h4 class="my-4">Cropped Picture</h4>
            <img class="my-4" src="{{ asset('storage/images/' . $cropped_pic_path) }}">
            @endif

            @if ($picture != null && $clustering_clicked == true)
            <h4 class="my-4">Clustered Picture</h4>
            <img class="my-4" src="{{ asset('storage/images/' . $clustered_pic_path) }}">
            @endif

            @if ($picture != null && $get_color_clicked == true)
            <h4 class="my-4">Colored Picture</h4>
            <img class="my-4" src="{{ asset('storage/images/' . $get_colored_pic_path) }}">
            @endif
        </div>
    </div>
</div>