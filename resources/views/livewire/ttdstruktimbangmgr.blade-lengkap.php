<div>


    @if (session('error'))
    <div class="pt-3">
        <div class="alert alert-danger">
            <span class="sr-only">WARNING</span>
            <div>
            <span class="font-medium">Danger alert!</span> {{ session("error") }}
            </div>
        </div>
    </div>
    @endif

    
    @if ($errors->any())
    <div class="pt-3">
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </div>
        
    @endif


    @if (session()->has('message'))
    <div class="pt-3">
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    </div>
        
    @endif


    <!-- START FORM -->
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <div>
            {{ now() }}
        </div>
        <div class="row">
            <div class="col">
                <div class="card m-auto   mt-3 text-white text-center bg-primary" style="max-width: 18rem;">
                    <h2>TTD Struk Timbang    </h2>
                </div>
            </div>
        </div>
        <form>
            <div class="row">
                <div class="col">
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">ID Transaction</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="transID" >
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">SPPB</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="sppbNo" disabled>
                        </div>
                    </div>

                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">SPM</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="spmNo" disabled>
                        </div>
                    </div>

                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Customer</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="custName" disabled>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Tgl Muat</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="tglMuat" disabled>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Barang</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="itemName" disabled>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Berat(Kg)</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="qtyKg" disabled>
                        </div>
                    </div>
        <div x-data="signaturePad()" class="mb-3 mt-3 row">         
            <label for="nama" class="col-sm-2 col-form-label">Tanda Tangan</label>
            <div class="col-sm-10">
                <canvas x-ref="signature_canvas" class="border rounded shadow" wire:model="signature">

                </canvas>
            </div>  
        </div>

        

                    {{-- <div class="row">
                                        <div class="col-lg-6">
                                            <div class="card border-primary mb-3" style="max-width: 150rem;">
                                                    <div class="card-header  text-center">Foto SIM / KTP</div>
                                                        <div class="card-body text-primary">
                                                            <img class="rounded float-start" style="width: 750px" src="{{ $simKtp}}">
                                                        </div>
                                                    </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="card border-primary mb-3" style="max-width: 150rem;">
                                                    <div class="card-header  text-center">Foto STNK</div>
                                                        <div class="card-body text-primary">
                                                                <img class="rounded float-start" style="width: 750px" src="{{ $stnk}}"  >
                                                        </div>
                                                    </div>
                                        </div>
                                        
                    </div>
                 --}}


                </div>
               
            </div>
            
           
            
            
            
          
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">
                   
                        <button type="button" class="btn btn-primary" name="submit"  wire:click="store()">APPROVE</button>
                   
                        <button type="button" class="btn btn-secondary" name="submit" wire:click="clear()">CLEAR</button>
                    
                </div>
            </div>
        </form>
    </div>
    <!-- AKHIR FORM -->
    

     <!-- START data sudah PGI-->
    <div class="my-3 p-3 bg-body rounded shadow-sm"  >
        <h1>Data Sudah PGI</h1>
        {{-- <div class="pb-3 pt-3">
            <input type="text" class="form-control mb-3 w-25" placeholder="Searching ... Tiket Muat" wire:model.live="katakunci">
        </div> --}}

        

        {{ $datasdhpgi->links() }}
        <table class="table table-striped table-sortable">
            <thead>
                <tr>
                    <th></th>
                    <th class="col-md">No</th>
                    <th class="col-md" >Tiket Muat</th>
                    <th class="col-md" >SPPB</th>
                    <th class="col-md" >SPM</th>
                    <th class="col-md" >Barang</th>
                    <th class="col-md" >Berat (Kg)</th>
                    <th class="col-md" >Customer</th>
                    <th class="col-md" >Plat No</th>
                    <th class="col-md" >Sopir</th>
                    <th class="col-md" >Tanda Tangan</th>
                    
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($datasdhpgi as $key => $value)
                <tr>
                    <td></td>
                    <td>{{ $datasdhpgi->firstItem() + $key }}</td>
                    <td>{{ $value->pendfNo }}</td>
                    <td>{{ $value->sppbNo }}</td>
                    <td>{{ $value->spmNo }}</td>
                    <td>{{ $value->itemName }}</td>
                    <td>{{ number_format($value->netto) }}</td>
                    <td>{{ $value->custName }}</td>
                    <td>{{ $value->carID }}</td>
                    <td>{{ $value->driver }}</td>
                   
                    
                    <td>
                        <a wire:click="edit({{ $value->trsID }})" class="btn btn-primary btn-sm">Pilih</a>
                    </td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>
    <!-- AKHIR data sudah PGI -->

    <!-- Signature-pad js -->
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
  
    <script>
        // const canvas = document.getElementById('signature_canvas');
        // const signaturePad = new SignaturePad(canvas);
        document.addEventListener('alpine:init', () => {
        Alpine.data('signaturePad', (value) => ({
            signaturePadInstance: null,
            
            init(){
                this.signaturePadInstance = new SignaturePad(this.$refs.signature_canvas);
            },
            upload(){
                @this.set('signature', this.signaturePadInstance.toDataURL('image/png'));
            }
        }))
    })
    </script>
                    
</div>
