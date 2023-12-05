    <div>
        @if ($successMessage)
        <div class="bg-green-200 text-green-800 p-2 rounded-md mb-4">{{ $successMessage }}</div>
        @endif

        @if ($errorMessage)
        <div class="bg-red-200 text-red-800 p-2 rounded-md mb-4">{{ $errorMessage }}</div>
        @endif

        <form class="  " wire:submit.prevent="store">
            <div class=" ">
                <label for="category" class="text-white"> Select a category </label>
                <select name="category" wire:model="category" id="category" class="text-black   border rounded-md w-full @error('category') border-red-500 @enderror">
                    <option value="" disabled selected>Select a category</option>
                    @foreach ($categories as $cat)
                    <option value="{{ $cat }}"> {{$cat}} </option>
                    @endforeach
                </select>
                @error('category')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror

            </div>
            <div class="py-4">
                <label for="images" class="block text-white">Choose an image or many images :</label>
                <input type="file" wire:model="images" id="images" class=" p-2 border rounded-md w-full @error('image') border-red-500 @enderror" multiple>
                @error('images')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md">
                Upload Image
            </button>
        </form>

    </div>

