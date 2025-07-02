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
                    <h2>Upload PGI </h2>
                </div>
            </div>
        </div>
        <form>
            <div class="row">
                <div class="col">
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label" hidden>ID Transaction</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="transID" hidden>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Driver</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="driver" disabled>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">plat No</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="carID" disabled>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Customer</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="custName" disabled>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Item</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="itemName" disabled>
                        </div>
                    </div>
                    <div class="card offset-2 mb-2" style="width: 26rem;">
                        <div class="col-sm-10">
                            <div class="card-body">
                                            
                                <label for="nama" class="col-sm-8 col-form-label">Upload Bukti PGI</label>
                                <input wire:model='buktiPGI' accept="image/png, image/jpeg" type="file" id="buktiPGI"  class="ring-a ring-inset ring-gray-300 bg-gray-100 text-gray-900 rounded block mb-4 mt-2  offset-0">
                                @if($buktiPGI)
                                    <img class="img-thumbnail rounded float-start offset-2" style="width: 300px; height: 300px" src="{{ $buktiPGI->temporaryUrl() }}" alt="">
                                @endif
                                <div wire:loading wire:target="buktiPGI">
                                    <span class="text-primary">Uploading ......</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    


                </div>
               
            </div>
            
           
            
            
            
          
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">
                   
                        <button type="button" class="btn btn-primary" name="submit" wire:click="update()">SIMPAN</button>
                   
                        <button type="button" class="btn btn-secondary" name="submit" wire:click="clear()">CLEAR</button>
                    
                </div>
            </div>
        </form>
    </div>
    <!-- AKHIR FORM -->
    

     <!-- START DATA sdh out-->
     <div class="my-3 p-3 bg-body rounded shadow-sm"  >
        <h1>Data Sudah Timbang Keluar</h1>
        <div class="pb-3 pt-3">
            <input type="text" class="form-control mb-3 w-25" placeholder="Searching ... Car ID" wire:model.live="katakunci">
        </div>

        

        {{ $sdhout->links() }}
        <table class="table table-striped table-sortable">
            <thead>
                <tr>
                    <th></th>
                    <th class="col-md">No</th>
                    <th class="col-md">SPM</th>
                    <th class="col-md" >driver</th>
                    <th class="col-md" >Car ID</th>
                    <th class="col-md" >Customer</th>
                    <th class="col-md" >Item Name</th>
                    <th class="col-md" >Bobot IN</th>
                    <th class="col-md" >Date IN</th>
                    
                    
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($sdhout as $key => $value)
                <tr>
                    <td></td>
                    <td>{{ $sdhout->firstItem() + $key }}</td>
                    <td>{{ $value->spmNo }}</td>
                    <td>{{ $value->driver }}</td>
                    <td>{{ $value->carID }}</td>
                    <td>{{ $value->custName }}</td>
                    <td>{{ $value->itemName }}</td>
                    <td>{{ $value->timbangin }}</td>
                    <td>{{ $value->jam_in }}</td>
                    
                    <td>
                        <a wire:click="edit({{ $value->trsID }})" class="btn btn-primary btn-sm">Upload PGI</a>
                    </td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>
    <!-- AKHIR DATA sdh out -->
    
</div>
