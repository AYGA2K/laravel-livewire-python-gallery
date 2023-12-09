    <div class=" container mx-auto text-white border-box">
        <div class="flex   gap-x-28 py-4  ">
            <div class="flex flex-col">
                <div class=" ">
                    @if ($picture != null)
                    <img id="main-pic" src="{{ asset('storage/' . $picture->name) }}" class="w-[500px] h-auto">
                 <button  class="bg-green-500 m-4  hover:bg-green-700 text-white font-bold py-2 px-4 rounded" wire:click="cropImage({{$picture->id}})">Crop this image</button>

                    @else
                    <h3>Please select an image from the uploaded files above.</h3>
                    @endif
                </div>
                <div class="flex gap-4 flex-wrap  cursor-pointer ">
@forelse ($similar_images as $image)
 <img src="{{ asset('storage/' . $image->name) }}" class="w-[150px] h-auto"   alt="Image {{ $image->id }}" wire:click="selectImage({{ $image->id }})" >
@empty
    <!-- Handle the case where $similar_images is empty -->
@endforelse
                </div>
            </div>
            <div class="flex flex-col gap-4">
                <h2>Histogram</h2>
                <canvas id="historgram_canvas"></canvas>
                <h2>Color Moment</h2>
                <canvas id="color_moment_canvas"></canvas>
                <h2>Clustering Data </h2>
                <canvas id="clustering_canvas"></canvas>
                <h2>Tamura</h2>
                <table class="border-box border border-gray-300 whitespace-pre-line " >
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
        // clustering canvas
         const clustering_canvas = document.getElementById('clustering_canvas');
          var clustringData= @json($ClusteringData);
                 new Chart(clustering_canvas, {
            type: 'bar',
            data: {
                labels: Object.keys(clustringData),
                datasets: [
                    {
                        label: 'Line 1',
                        data: Object.values(clustringData),
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
                        borderColor: 'cyan',
                        borderWidth: 1,
                        pointBorderWidth: 0.2,
                        fill: false
                    },
                    {
                        label: 'Line 2',
                        data: dataR,
                        borderColor: 'rgb(220,20,60)',
                        borderWidth: 1,
                        pointBorderWidth: 0.2,
                        fill: false
                    },
                    {
                        label: 'Line 3',
                        data: dataG,
                        borderColor: 'green',
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
                        suggestedMax: 255,
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



</script>
