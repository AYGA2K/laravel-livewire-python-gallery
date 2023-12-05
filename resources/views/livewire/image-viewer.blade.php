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

<div  class="relative p-4 ">
    <button wire:click="deleteImage({{ $image->id }})" class="absolute top-2 right-2 text-red-500">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <img wire:click="processImage({{$image->id}})"  src="{{ asset('storage/' . $image->name) }}" alt="{{ $image->name }}" class="w-full cursor-pointer h-auto">

</div>

        @endforeach
    </div>



</div>
