<div class="mx-auto">
    @if ($successMessage)
    <div class="bg-green-200 text-green-800 p-2 rounded-md mb-4">{{ $successMessage }}</div>
    @endif

    @if ($errorMessage)
    <div class="bg-red-200 text-red-800 p-2 rounded-md mb-4">{{ $errorMessage }}</div>
    @endif

    <form wire:submit.prevent="store">
        <div class="mb-4">
            <label for="categoryName" class="block text-white text-sm font-bold mb-2">Category Name:</label>
            <input wire:model="categoryName" type="text" id="categoryName" name="categoryName" class="p-2 border text-black rounded-md w-full @error('categoryName') border-red-500 @enderror">
            @error('categoryName')
            <p class="text-black text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md">
            Add Category
        </button>
    </form>
</div>
