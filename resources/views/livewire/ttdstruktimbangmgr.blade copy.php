<div>
   
      
     
        <div x-data="signaturePad()" class="relative shadow-xl bg-white rounded-lg p-6 flex flex-col gap-4">
            <h1 class="text-xl font-semibold text-gray-700 flex items-center justify-between"></h1>
            <div>
                <canvas x-ref="signature_canvas"  class="border rounded shadow"  wire.model="sig1">

                </canvas>
            </div>
            
        </div>


        <button wire:click="submit" class="text-black">
            Submit
        </button>
      <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

        <script>
        //    const canvas = document.getElementById('signature_canvas');
        //    const signaturePad = new SignaturePad(canvas);
             document.addEventListener('alpine:init', () => {
        Alpine.data('signaturePad', (value) => ({
            signaturePadInstance: null,
            value: value,
            init(){
                this.signaturePadInstance = new SignaturePad(this.$refs.signature_canvas);
                this.signaturePadInstance.addEventListener("endStroke", ()=>{
                   this.value = this.signaturePadInstance.toDataURL('image/png');
                });
            },
        }))
    })

        </script>


      
        </script>
    

</div>
