<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div class="mb-3 mt-3 row">
                                        
        <div class="col-sm-10" >
            <img  src="http://10.20.12.208/cgi-bin/encoder?USER=apps&PWD=Tebumas12&GET_STREAM" id="video"  >
            <button id="take-snapshot" wire:click.prevent>take snapshot</button>
            
        </div>
    </div>

    <div class="mb-3 mt-3 row">
        
        <div class="col-sm-10" >
            {{-- <input type="image" src="http://10.20.12.208/cgi-bin/encoder?USER=apps&PWD=Tebumas12&GET_STREAM"   wire:model="output"> --}}
            <canvas id="canvas"  ></canvas>
            <div id="dataurl-header">Image Data url</div>
            <textarea id="dataurl" readonly></textarea>
            <div id="results3" class="d-none"></div>
        </div>
    </div>
</body>

</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
        <script>
            let video = document.querySelector("#video");
            let btn_take_snapshot = document.querySelector("#take-snapshot");
            let canvas = document.getElementById("canvas");
            let dataurl = document.querySelector("#dataurl");
            let dataurl_container =  document.querySelector("#dataurl-container");
            canvas.crossOrigin= 'anonymous';
            btn_take_snapshot.addEventListener('click', ()=> {
                canvas.width = 200;
                canvas.height = 200;
                canvas.getContext('2d').drawImage(video,0,0, canvas.width, canvas.height);
                let image_data_url = canvas.toDataURL('image/png');
                
                var data = image_data_url.getImageData(0,0,0,0);
                dataurl.value = data;
                dataurl_container.style.display = 'block';
            });



            function take_snapshot()
            {
                //take snapshot and get image data
                Webcam.snap(function(data_uri) {
                    image1 = data_uri;
                    //display result image
                    var lokasi = $("#lokasi").val();
                    $('#results').html('<img src="' + data_uri + '" class="d-block mx-auto rounded"/>');

                    var raw_image_data = data_uri.replace(/^data\:image\/\w+\;base64\,/, '');
                    $('#photoStore').val(raw_image_data);
                });

                

                $('#results').removeClass('d-none');

            }

        </script>
</html>