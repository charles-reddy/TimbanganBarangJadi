<div>
    
    <div class="mb-3">
        <label for="tanda_tangan">Tanda Tangan</label>
        <div class="d-block form-control mb-2">
            <canvas id="signature-pad" class="signature-pad" wire.model="sig1">

            </canvas>
            

        </div>
        <textarea name="signature" id="signature64" class="d-none" wire.model="sig2"></textarea>
            @error('signature')
                <div class="text-danger">{{ $message }}</div>
            @enderror
            <button type="button" id="clear" class="btn btn-sm btn-secondary">Clear</button>

    </div>

    <button id="form-absen" type="submit" class="btn btn-primary">Submit</button>
    
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
      <script src="{{ asset('js/signature.min.js') }}"></script>
    
      <script>
            $(function() {

                // Set signature pad width
                let sig = $('#signature-pad').parent().width(280);
                $('#signature-pad').attr('width',sig);

                // Set Canvas Color
                let signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
                    backgroundColor: 'rgb(255, 255, 255, 0)',
                    penColor: 'rgb(0, 0, 0)',
                });

                // Fill Signatur to text area
                $('canvas').on('mouseup touchend', function() {
                    $('#signature64').val(signaturePad.toDataURL());
                });

                //clear signature
                $('#clear').on('click', function(e) {
                    e.preventDefault();
                    signaturePad.clear();
                    $('#signature64').val('');
                });

                //submit form
                $('#form-absen').click(function() {
                    var ttd1 = $("#signature64").val();
                    $.ajax({
                        type:'POST'
                        , url:'/ttdstore'
                        
                        , data:{
                            _token:"{{ csrf_token() }}"
                            , image1:ttd1
                            
                        }
                    });
                    setTimeout("location.href='/ttdstruktimbangmgr'", 3000 );

                });
            });
      </script>

</div>
