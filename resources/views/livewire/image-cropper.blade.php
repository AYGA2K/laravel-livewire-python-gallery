<div class="container mx-auto my-4 ">
    <img id="main-pic" src="{{ asset('storage/' . $picture->name) }}" class="w-[500px]  h-auto">
    <div class="container mx-auto my-4 ">
        <input id="x" class="w-[100px]  text-black" type="text" wire:model="cropX" disabled />
        <input id="y" class="w-[100px] text-black" type="text" wire:model="cropY" disabled />
        <input id="width" class="w-[100px] text-black" type="text" wire:model="cropWidth" disabled />
        <input id="height" class="w-[100px] text-black" type="text" wire:model="cropHeight" disabled />
        <button id="crop_button" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" type="submit">Crop</button>

    </div>
    <div class="container mx-auto my-4 flex justify-center items-center "> <img id="cropped-pic" src="" class="w-[500px]   h-auto">
    </div>
    <script>
        const imageToCrop = document.querySelector("#main-pic");

        if (imageToCrop) {
            const cropper = new Cropper(imageToCrop, {
                aspectRatio: 1,
                crop: function(e) {
                    var data = e.detail;
                    document.getElementById("x").value = data.x;
                    document.getElementById("y").value = data.y;
                    document.getElementById("width").value = data.width;
                    document.getElementById("height").value = data.height;
                }
            });

            const sendCropRequest = async () => {
                const imageName = "{{$picture->name}}";
                const x = Math.floor(document.getElementById("x").value);
                const y = Math.floor(document.getElementById("y").value);
                const width = Math.floor(document.getElementById("width").value);
                const height = Math.floor(document.getElementById("height").value);

                const url = `http://localhost:5000/cropImage?imageName=${imageName}&x=${x}&y=${y}&width=${width}&height=${height}`;

                try {
                    const response = await fetch(url);

                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }

                    const data = await response.json();
                    console.log(data.cropped_image_path);
                    const newImage = document.getElementById("cropped-pic").cloneNode(true);
                    newImage.src = "http://localhost:8000/storage/" + data.cropped_image_path;
                    document.getElementById("cropped-pic").replaceWith(newImage);
                } catch (error) {
                    console.error("Fetch error: ", error);
                }
            };

            document.getElementById("crop_button").addEventListener("click", sendCropRequest);
        }
    </script>
</div>
