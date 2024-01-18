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
    </div>
</div>
