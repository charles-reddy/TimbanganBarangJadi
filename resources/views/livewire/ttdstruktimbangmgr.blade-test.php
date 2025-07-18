<div>
    <meta name="csrf-token" content="{{ csrf_token() }}">
      <script src="http://keith-wood.name/js/jquery.signature.js"></script>
      <link rel="stylesheet" href="http://keith-wood.name/css/jquery.signature.css">
     
        <div  class="relative shadow-xl bg-white rounded-lg p-6 flex flex-col gap-4">
            <h1 class="text-xl font-semibold text-gray-700 flex items-center justify-between"></h1>
            <div>
                <canvas id="sign"  class="border rounded shadow">

                </canvas>
            </div>
            <div>
                <textarea name="signed" id="signature" cols="30" rows="10"></textarea>
            </div>
            
        </div>


        <button id="takeabsen" class= "btn btn-primary btn-block">
                        <ion-icon name="camera-outline"></ion-icon>    
                        Absen Masuk 
        </button>
      

        <script>
            // const canvas = document.getElementById('signature_canvas');
            // const signaturePad = new SignaturePad(canvas);
            // let dataURL = canvas.toDataURL('image/png').replace(/^data:image\/jpeg;base64,/, "");
            var sign = $('#sign').signature({syncField:'#signature', syncFormat:'PNG'})


        </script>


        
    

</div>
