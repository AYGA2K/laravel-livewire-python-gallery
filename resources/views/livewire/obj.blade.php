<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                @if ($successMessage)
                <div class="bg-green-200 text-green-800 p-2 rounded-md mb-4">{{ $successMessage }}</div>
                @endif

                @if ($errorMessage)
                <div class="bg-red-200 text-red-800 p-2 rounded-md mb-4">{{ $errorMessage }}</div>
                @endif

                <form class="" wire:submit.prevent="store">
                    <div class="py-4">
                        <label for="objFile" class="block text-white">Choose .obj file :</label>
                        <input type="file" wire:model="objFile" id="objFile" class="p-2 border rounded-md w-full @error('objFile') border-red-500 @enderror">
                        @error('objFile')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md">
                        Upload .obj File
                    </button>
                </form>

            </div>
        </div>

        <div class=" py-4 flex justify-center items-center gap-2 flex-wrap">
            @if ($isProcessing)
            <div class="flex  text-white h-[50vh] text-4xl  items-center justify-center">
                <svg class="animate-spin h-5 w-5 mr-3 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <circle cx="12" cy="12" r="10" stroke-width="6" />
                    <path d="M22 12h-4l-3 9M2 12h4l3 9" stroke-width="6" />
                </svg>
                <p>Loading...</p>

            </div>
            @else
            @foreach ($images as $image)
            @if (Storage::exists('public/' . $image))
            <div class="w-1/5">
                <img src="{{ asset('storage/' . $image) }}" alt="Image" class="max-w-full h-auto">
            </div>
            @endif
            @endforeach
            @endif
        </div>
    </div>
</div>
