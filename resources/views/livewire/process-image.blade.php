    <div class="container mx-auto text-white border-box">
        <div class="flex mt-12 space-x-12">
            <div class="flex flex-col gap-[20px] w-[70vw]">
                <div class="w-[70%]">
                    @if ($picture != null)
                    <img id="main-pic" src="{{ asset('storage/' . $picture->name) }}" class="w-[500px] h-auto">
                    <form class="flex disabled place-content-between" wire:submit="crop">
                        <input id="x" class="w-[100px] text-black " type="text" wire:model="cropX" />
                        <input id="y" class="w-[100px] text-black " type="text" wire:model="cropY" value="" disabled />
                        <input id="width" class="w-[100px] text-black " type="text" wire:model="cropWidth" value="" disabled/>
                        <input  id="height" class="w-[100px] text-black " type="text" wire:model="cropHeight" value="" disabled/>
                        <button id="crop_button" type="submit"> Crop </button>
                    </form>
                    @else
                    <h3>Please select an image from the uploaded files above.</h3>
                    @endif
                </div>
                <div class="flex flex-wrap gap-[20px]">
                    @foreach ( $similar_images as $image )
                    <img src="{{ asset('storage/' . $image->name ) }}" class="w-[150px] h-auto">
                    @endforeach
                </div>
            </div>
            <div class="w-[30vw]">
                <h2>Histogram</h2>
                <h6></h6>
                <canvas id="historgram_canvas"></canvas>
                <h2>Color Moment</h2>
                <h6></h6>
                <canvas id="color_moment_canvas"></canvas>
                <h2>Indices</h2>
                <h6>This is a damn index </h6>
                <h2>Trauma</h2>
                <table class="border-box border border-gray-300 mt-[40px] whitespace-pre-line " >
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">contrast</th>
                            <th class="py-2 px-4 border-b">directionality</th>
                            <th class="py-2 px-4 border-b">coarsness</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-2 px-4 border-b">{{ $contrast  }}</td>
                            <td class="py-2 px-4 border-b">{{ $directionality  }}</td>
                            <td class="py-2 px-4 border-b">{{ $coarseness }}</td>
                        </tr>
                    </tbody>
                </table>
                <table class="border-box border border-gray-300 mt-[40px] whitespace-pre-line " >
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">linelikeness</th>
                            <th class="py-2 px-4 border-b">regularity</th>
                            <th class="py-2 px-4 border-b">roughness</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>

                            <td class="py-2 px-4 border-b">{{ $linelikeness }}</td>
                            <td class="py-2 px-4 border-b">{{ $regularity }}</td>
                            <td class="py-2 px-4 border-b">{{ $roughness }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <script type="text/javascript">

        // histogram canvas
        const ctx = document.getElementById('historgram_canvas');

        var dataR = {{$dataR}}; 
        var dataG = {{$dataG}};
        var dataB = {{$dataB}};


        var labels = [] ;
        
        for (iii = 0 ; iii < 255 ; iii++)
            labels.push(iii);
        
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Line 1',
                        data: dataB,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        pointBorderWidth: 0.2,
                        fill: false
                    },
                    {
                        label: 'Line 2',
                        data: dataR,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        pointBorderWidth: 0.2,
                        fill: false
                    },
                    {
                        label: 'Line 3',
                        data: dataG,
                        borderColor: 'rgba(0, 0, 0, 1)',
                        borderWidth: 1,
                        pointBorderWidth: 0.2,
                        fill: false
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        stacked: false,
                        suggestedMin: 0,
                        suggestedMax: 255
                    }
                }
            }
        });


    

        // color moment canvas
        const color_moment_canvas = document.getElementById('color_moment_canvas');
        let color_moment_data = JSON.parse(@json($color_moment_data));

        new Chart(color_moment_canvas, {
            type: 'bar',
            data: {
                labels: Object.keys(color_moment_data),
                datasets: [
                    {
                        label: 'Line 1',
                        data: Object.values(color_moment_data),
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        pointBorderWidth: 0.2,
                        fill: false
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        stacked: false,
                    }
                }
            }
        });



        croppedData = {};
        const imageToCrop = document.querySelector("#main-pic");
        if (imageToCrop) {
            const cropper = new Cropper(imageToCrop, {
                aspectRatio: 1,
                crop: function (e) {
                    var data = e.detail;
                    croppedData = {
                        x: data.x,
                        y: data.y,
                        width: data.width,
                        height: data.height
                    };
                    document.getElementById('x').value = data.x;
                    document.getElementById('y').value = data.y;
                    document.getElementById('width').value = data.width;
                    document.getElementById('height').value = data.height;
                }
            });
        } else {
            console.error("Image element not found");
        }
        // const cropButton = document.querySelector("#crop_button"); // Replace with your actual button ID
        // cropButton.addEventListener("click", function () {

        //     console.log("Cropped Data:", croppedData);
        // });
</script>
