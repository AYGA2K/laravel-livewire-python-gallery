<div>
                    <img id="main-pic" src="{{ asset('storage/' . $picture->name) }}" class="w-[500px] h-auto">

    <form class="flex my-4 px-11 place-content-between" wire:submit.prevent="crop">
        <input id="x" class="w-[100px] text-black" type="text" wire:model="cropX" disabled />
        <input id="y" class="w-[100px] text-black" type="text" wire:model="cropY" disabled />
        <input id="width" class="w-[100px] text-black" type="text" wire:model="cropWidth" disabled />
        <input id="height" class="w-[100px] text-black" type="text" wire:model="cropHeight" disabled />
        <button id="crop_button"  class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" type="submit">Crop</button>
    </form>
      <script  >

        const imageToCrop = document.querySelector("#main-pic");

        if (imageToCrop) {
            const cropper = new Cropper(imageToCrop, {
                aspectRatio: 1,
                crop: function (e) {
                    var data = e.detail;

                    document.getElementById("x").value = data.x;
                    document.getElementById("y").value = data.y;
                    document.getElementById("width").value = data.width;
                    document.getElementById("height").value = data.height;

                }
            });
        } else {
            console.error("Image element not found");
        }
</script>
</div>


