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

        <div wire:click="processImage({{$image->id}})" class=" p-4 cursor-pointer">
            <img src="{{ asset('storage/' . $image->name) }}" alt="{{ $image->name }}" class="w-full h-auto">
        </div>

        @endforeach
    </div>



</div>
