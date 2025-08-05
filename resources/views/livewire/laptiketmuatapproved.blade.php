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



    {{-- form pembatalan --}}

    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <div>
            {{ now() }} - {{ request()->ip() }}
        </div>
        <div class="row">
            <div class="col">
                <div class="card m-auto   mt-3 text-white text-center bg-primary" style="max-width: 18rem;">
                    <h2>Cancel Approval Tiket Muat </h2>
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
                        <label for="nama" class="col-sm-2 col-form-label">Tiket Muat</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="tiketMuat" disabled>
                        </div>
                    </div>
                    
                </div>
               
            </div>
            
           
            
            
            
          
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">
                   
                        <button type="button" class="btn btn-primary" name="submit" wire:click="store()">Cancel</button>
                   
                        <button type="button" class="btn btn-secondary" name="submit" wire:click="clear()">CLEAR</button>
                    
                </div>
            </div>
        </form>
    </div>



    {{-- start report --}}
    <div class="card-body">
        <div class="card-body table-responsive p-0">
            <div class="my-3 p-3 bg-body rounded shadow-sm"  >
                <h1></h1>
                <div class="mb-3 row">
                    <div class="col">
                        <div class="card m-auto   mt-3 text-white text-center bg-primary" style="max-width: 30rem;">
                            <h2>Tiket Muat - Approved </h2>
                        </div>
                    </div>
                            
                            <div class="row mt-4">
                                <div class="col-sm-4">
                                    <input type="text" class="form-control mb-3 w-50" placeholder="Search base Tiket Muat ..." wire:model.live="katakunci">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control mb-3 w-50" placeholder="Search base customer ..." wire:model.live="katacust">
                                </div>
                                <div class="col-sm-1">
                                    <label for="">Filter Tgl Muat</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="date" id="tglin" class="form-control  mb-3 w-50"  wire:model.live="tglMuat">
                                </div>
                          
                            </div>

                
                        
                        {{ $datatiketmuat->links() }}
                    
                    <div class="d-flex justify-content-left">
                    
                        <table class="table table-striped table-sortable w-75 p-3 ">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="col-md-0">No Tiket Muat</th>
                                    <th class="col-md-0">SPPB No</th>
                                    <th class="col-md-0">Customer</th>
                                    <th class="col-md-0">Berat</th>
                                    <th class="col-md-0">Karung</th>
                                    <th class="col-md-0">Tgl Muat</th>
                                    <th class="col-md-0">Plat No</th>
                                    <th class="col-md-0">Approved</th>
                                    <th class="col-md-0">Sudah di Pabrik</th>
                                    
                                    <th class="col-md-1"  wire:click="Pilih" >Pilih </th>
                                    <th class="col-md-1"  wire:click="Pilih" >Cancel </th>
                                
                                    
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach ($datatiketmuat as $key )
                                <tr>
                                    <td></td>
                                    <td>{{ $key->pendfNo }}</td>
                                    <td>{{ $key->sppbNo }}</td>
                                    <td>{{ $key->custName }}</td>
                                    <td>{{ $key->tmQtyKg }}</td>
                                    <td>{{ $key->tmQtyKarung }}</td>
                                    <td>{{ $key->tglMuat }}</td>
                                    <td>{{ $key->tmCarID }}</td>
                                    <td> @php
                                        if ($key->isMktApp){
                                            echo 'Yes';
                                        } else {
                                            echo 'no';
                                        }

                                        @endphp
                                        </td>
                                    <td> @php
                                        if ($key->isSecCek){
                                            echo 'Yes';
                                        } else {
                                            echo 'no';
                                        }

                                        @endphp
                                    </td>
                                    
                                    
                                    <td>
                                        <a wire:click="edit('{{ $key->id }}')" class="btn btn-warning btn-sm">Pilih</a>
                                    </td>

                                    <td>
                                        <a wire:click="cancel('{{ $key->id }}')" class="btn btn-warning btn-sm">Cancel</a>
                                    </td>
                                </tr>
                                @endforeach
                                
                            </tbody>
                        </table>

                        
                    </div>
                    <div class="card-body table-responsive p-0">
                            <div class="row">
                                    
                                    <form >
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3 mt-3 row">
                                                    <label for="nama" class="col-sm-2 col-form-label">Tiket Muat</label>
                                                    
                                                        <input type="text" class="form-control w-50" wire:model="transID" hidden>
                                                    
                                                    {{-- <div class="col-sm-10">
                                                        <input type="text" class="form-control w-25 mb-2" id="tta_number" wire:model="tiketMuat" disabled>
                                                            <a href="/cetaktiket/{{ $transID }} " class="btn btn-primary" target="_blank" >cetak</a>
                                                    </div> --}}
                                                </div>
                                                
                                                
                                            
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="card border-primary mb-3" style="max-width: 200rem;">
                                                            <div class="card-header  text-center">Foto SIM / KTP</div>
                                                                <div class="card-body text-primary">
                                                                    <img class="rounded float-start" style="width: 750px" src="{{ $simKtp}}">
                                                                </div>
                                                            </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="card border-primary mb-3" style="max-width: 200rem;">
                                                            <div class="card-header  text-center">Foto STNK</div>
                                                                <div class="card-body text-primary">
                                                                        <img class="rounded float-start" style="width: 750px" src="{{ $stnk}}"  >
                                                                </div>
                                                            </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
