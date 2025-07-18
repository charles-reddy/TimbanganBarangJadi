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
                    <h2>Segel Truk </h2>
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
                        <label for="nama" class="col-sm-2 col-form-label" hidden>ID Timbangan</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="scaleID" hidden>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">SPM</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="spmNo" disabled>
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
                            <input type="text" class="form-control w-50" wire:model="itemType" hidden>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Jenis Truk</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="jenisTruk" disabled>
                            <input type="text" class="form-control w-50" wire:model="jenisTruk1" hidden>
                        </div>
                    </div>
                    <div class="card text-center">
                        <div class="card-body offset-2">
                            <div class="mb-3 mt-3 row ">
                                <div class="card mr-2" style="width: 26rem;">
                                    <div class="col-sm-10">
                                        <div class="card-body">
                                                <label  class="col-sm-2 col-form-label text-center">Seal 1</label>
                                            @if ( $jenisTruk1  == 1 )
                                                <input type="text" class="form-control w-50 offset-3" wire:model="sealNo1" >
                                            @elseif ( $jenisTruk1  == 2)
                                                    <input type="text" class="form-control w-50 offset-3" wire:model="sealNo1"  >
                                            @elseif ( $jenisTruk1  == 3)
                                                    <input type="text" class="form-control w-50 offset-3" wire:model="sealNo1"  >
                                            @elseif ( $jenisTruk1  == 4)
                                                    <input type="text" class="form-control w-50 offset-3" wire:model="sealNo1"  >
                                            @elseif ( $jenisTruk1  == 5)
                                                    <input type="text" class="form-control w-50 offset-3" wire:model="sealNo1"  >
                                            @elseif ( $jenisTruk1  == 6)
                                                    <input type="text" class="form-control w-50 offset-3" wire:model="sealNo1"  >
                                            @elseif ( $jenisTruk1  == 7)
                                                    <input type="text" class="form-control w-50 offset-3" wire:model="sealNo1"  >
                                            @else
                                                <input type="text" class="form-control w-50 offset-3" wire:model="sealNo1" disabled >
                                            @endif
                                            <input wire:model='fotoSealNo1' accept="image/png, image/jpeg" type="file" id="fotoSealNo1"  class="ring-a ring-inset ring-gray-300 bg-gray-100 text-gray-900 rounded block mb-4 mt-2  offset-1">
                                            @if($fotoSealNo1)
                                                <img class="img-thumbnail rounded float-start offset-2" style="width: 300px; height: 300px" src="{{ $fotoSealNo1->temporaryUrl() }}" alt="">
                                            @endif
                                            <div wire:loading wire:target="fotoSealNo1">
                                                <span class="text-primary">Uploading ......</span>
                                            </div>
                                        </div>   
                                    </div>
                                    
                                    
                                </div>

                                <div class="card mr-2" style="width: 26rem;">
                                    <div class="col-sm-10">
                                        <div class="card-body">
                                            <label  class="col-sm-2 col-form-label offset-2">Seal 2 </label>
                                                @if ( $jenisTruk1  == 1)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo2" disabled >
                                                @elseif ( $jenisTruk1  == 2)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo2"  >
                                                @elseif ( $jenisTruk1  == 4)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo2"  >
                                                @elseif ( $jenisTruk1  == 4)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo2"  >
                                                @elseif ( $jenisTruk1  == 5)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo2"  >
                                                @elseif ( $jenisTruk1  == 6)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo2"  >
                                                @elseif ( $jenisTruk1  == 7)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo2"  >
                                                @elseif ( $jenisTruk1  == 0)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo2" disable >
                                                @else
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo2" disable  >
                                                @endif
                                            <input wire:model='fotoSealNo2' accept="image/png, image/jpeg" type="file" id="fotoSealNo2"  class="ring-a ring-inset ring-gray-300 bg-gray-100 text-gray-900 rounded block mb-4 mt-2  offset-0">
                                            @if($fotoSealNo2)
                                                <img class="img-thumbnail rounded float-start offset-2" style="width: 300px; height: 300px" src="{{ $fotoSealNo2->temporaryUrl() }}" alt="">
                                            @endif
                                            <div wire:loading wire:target="fotoSealNo2">
                                                <span class="text-primary">Uploading ......</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card mr-2" style="width: 26rem;">
                                    <div class="col-sm-10">
                                        <div class="card-body">
                                            <label  class="col-sm-2 col-form-label offset-2">Seal 3</label>
                                            
                                                @if ( $jenisTruk1 == 1)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo3" disabled >
                                                @elseif ( $jenisTruk1  == 2)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo3"  disabled>
                                                @elseif ( $jenisTruk1  == 3)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo3"   disabled>
                                                @elseif ( $jenisTruk1  == 4)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo3"   disabled>
                                                @elseif ( $jenisTruk1  == 5)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo3"  >
                                                @elseif ( $jenisTruk1  == 6)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo3"  >
                                                @elseif ( $jenisTruk1  == 7)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo3"  >
                                                @else
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo3"  disabled>
                                                @endif
                                                <input wire:model='fotoSealNo3' accept="image/png, image/jpeg" type="file" id="fotoSealNo3"  class="ring-a ring-inset ring-gray-300 bg-gray-100 text-gray-900 rounded block mb-4 mt-2  offset-0">
                                                @if($fotoSealNo3)
                                                    <img class="img-thumbnail rounded float-start offset-2" style="width: 300px; height: 300px" src="{{ $fotoSealNo3->temporaryUrl() }}" alt="">
                                                @endif
                                                <div wire:loading wire:target="fotoSealNo3">
                                                    <span class="text-primary">Uploading ......</span>
                                                </div>

                                        </div>
                                    </div>
                                </div>

                                

                            </div>
                        </div>
                        
                        <div class="card-body offset-3">
                            <div class="mb-3 mt-3 row ">
                            <div class="card mr-2" style="width: 26rem;">
                                    <div class="col-sm-10">
                                        <div class="card-body">
                                            <label  class="col-sm-2 col-form-label offset-2">Seal 4</label>
                                            
                                                
                                                @if ( $jenisTruk1 == 1)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo4" disabled >
                                                @elseif ( $jenisTruk1  == 2)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo4"  disabled>
                                                @elseif ( $jenisTruk1  == 3)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo4"   disabled>
                                                @elseif ( $jenisTruk1  == 4)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo4"   disabled>
                                                @elseif ( $jenisTruk1  == 5)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo4"  >
                                                @elseif ( $jenisTruk1  == 6)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo4"  >
                                                @elseif ( $jenisTruk1  == 7)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo4"   disabled>
                                                @else
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo4"  disabled>
                                                @endif
                                                <input wire:model='fotoSealNo4' accept="image/png, image/jpeg" type="file" id="fotoSealNo4"  class="ring-a ring-inset ring-gray-300 bg-gray-100 text-gray-900 rounded block mb-4 mt-2  offset-0">
                                                @if($fotoSealNo4)
                                                    <img class="img-thumbnail rounded float-start offset-2" style="width: 300px; height: 300px" src="{{ $fotoSealNo4->temporaryUrl() }}" alt="">
                                                @endif
                                                <div wire:loading wire:target="fotoSealNo4">
                                                    <span class="text-primary">Uploading ......</span>
                                                </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="card mr-2" style="width: 26rem;">
                                    <div class="col-sm-10">
                                        <div class="card-body">
                                            <label  class="col-sm-2 col-form-label offset-2">Seal 5</label>
                                        
                                                
                                                @if ( $jenisTruk1 == 1)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo5" disabled >
                                                @elseif ( $jenisTruk1  == 2)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo5"  disabled>
                                                @elseif ( $jenisTruk1  == 3)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo5"   disabled>
                                                @elseif ( $jenisTruk1  == 4)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo5"   disabled>
                                                @elseif ( $jenisTruk1  == 5)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo5"  >
                                                @elseif ( $jenisTruk1  == 6)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo5"  >
                                                @elseif ( $jenisTruk1  == 7)
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo5"   disabled>
                                                @else
                                                    <input type="text" class="form-control w-50 offset-4" wire:model="sealNo5"  disabled>
                                                @endif
                                                <input wire:model='fotoSealNo5' accept="image/png, image/jpeg" type="file" id="fotoSealNo5"  class="ring-a ring-inset ring-gray-300 bg-gray-100 text-gray-900 rounded block mb-4 mt-2  offset-0">
                                                @if($fotoSealNo5)
                                                    <img class="img-thumbnail rounded float-start offset-2" style="width: 300px; height: 300px" src="{{ $fotoSealNo5->temporaryUrl() }}" alt="">
                                                @endif
                                                <div wire:loading wire:target="fotoSealNo5">
                                                    <span class="text-primary">Uploading ......</span>
                                                </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                                
                                                    <div class="card border-primary mb-3 offset-4" style="max-width: 30rem;">
                                                        <div class="card-header  ">Jenis Truk</div>
                                                            <div class="card-body text-primary">
                                                                <img class="rounded float-start" style="width: 1150px" src="{{ $fototruk}}">
                                                            </div>
                                                        </div>
                                   
                        </div>
                    </div>
                    
               
            </div>
            
           
            
            
            
          
            <div class="mb-3 mt-2 row">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">
                   
                        <button type="button" class="btn btn-primary" name="submit" wire:click="store()">SAVE</button>
                   
                        <button type="button" class="btn btn-secondary" name="submit" wire:click="clear()">CLEAR</button>
                    
                </div>
            </div>
        </form>
    </div>
    <!-- AKHIR FORM -->
    
    
     

    <!-- START DATA Molases-->
    <div class="my-3 p-3 bg-body rounded shadow-sm"  >
        <h1> Segel Truk Molases</h1>
        <!-- <div class="pb-3 pt-3">
            <input type="text" class="form-control mb-3 w-25" placeholder="Searching ... Plat no" wire:model.live="katakunci">
        </div> -->

        

        {{ $trukmol->links() }}
        <table class="table table-striped table-sortable">
            <thead>
                <tr>
                    <th></th>
                    <th class="col-md">No</th>
                    <th class="col-md">SPM</th>
                    <th class="col-md" >Driver</th>
                    <th class="col-md" >Car ID</th>
                    <th class="col-md" >Customer</th>
                    <th class="col-md" >Item </th>
                    <th class="col-md" >Truck Type </th>
                    <th class="col-md" >Date In </th>
                    
                    
                    
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($trukmol as $key => $value)
                <tr>
                    <td></td>
                    <td>{{ $trukmol->firstItem() + $key }}</td>
                    <td>{{ $value->spmNo }}</td>
                    <td>{{ $value->driver }}</td>
                    <td>{{ $value->carID }}</td>
                    <td>{{ $value->custName }}</td>
                    <td>{{ $value->itemName }}</td>
                    <td>{{ $value->jenisTruk }}</td>
                    <td>{{ $value->jam_in }}</td>
                    
                    
                    <td>
                        <a wire:click="edit({{ $value->id }})" class="btn btn-primary btn-sm">Seal</a>
                    </td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>
    <!-- AKHIR DATA Molases -->

    <!-- START DATA Gula-->
    <div class="my-3 p-3 bg-body rounded shadow-sm"  >
        <h1> Segel Truk Gula</h1>
        <!-- <div class="pb-3 pt-3">
            <input type="text" class="form-control mb-3 w-25" placeholder="Searching ... Plat no" wire:model.live="katakunci">
        </div> -->

        

        {{ $trukgula->links() }}
        <table class="table table-striped table-sortable">
            <thead>
                <tr>
                    <th></th>
                    <th class="col-md">No</th>
                    <th class="col-md" >SPM</th>
                    <th class="col-md" >Driver</th>
                    <th class="col-md" >Car ID</th>
                    <th class="col-md" >Customer</th>
                    <th class="col-md" >Item </th>
                    <th class="col-md" >Truck Type </th>
                    <th class="col-md" >Date In </th>
                    
                    
                    
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($trukgula as $key => $value)
                <tr>
                    <td></td>
                    <td>{{ $trukgula->firstItem() + $key }}</td>
                    <td>{{ $value->spmNo }}</td>
                    <td>{{ $value->driver }}</td>
                    <td>{{ $value->carID }}</td>
                    <td>{{ $value->custName }}</td>
                    <td>{{ $value->itemName }}</td>
                    <td>{{ $value->jenisTruk }}</td>
                    <td>{{ $value->jam_in }}</td>
                    
                    
                    <td>
                        <a wire:click="edit({{ $value->id }})" class="btn btn-primary btn-sm">Seal</a>
                    </td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>
    <!-- AKHIR DATA Gula -->
    

    <!-- START Sudah segel-->
    <div class="my-3 p-3 bg-body rounded shadow-sm"  >
        <h1> Truk Sudah Segel</h1>
        <div class="pb-3 pt-3">
            <input type="text" class="form-control mb-3 w-25" placeholder="Searching ... Plat no" wire:model.live="katakunci">
        </div>

        

        {{ $truksdhsegel->links() }}
        <table class="table table-striped table-sortable">
            <thead>
                <tr>
                    <th></th>
                    <th class="col-md">No</th>
                    <th class="col-md" >SPM</th>
                    <th class="col-md" >Driver</th>
                    <th class="col-md" >Car ID</th>
                    <th class="col-md" >Customer</th>
                    <th class="col-md" >Item </th>
                    <th class="col-md" >Truck Type </th>
                    <th class="col-md" >Date In </th>
                    
                    
                    
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($truksdhsegel as $key => $value)
                <tr>
                    <td></td>
                    <td>{{ $truksdhsegel->firstItem() + $key }}</td>
                    <td>{{ $value->spmNo }}</td>
                    <td>{{ $value->driver }}</td>
                    <td>{{ $value->carID }}</td>
                    <td>{{ $value->custName }}</td>
                    <td>{{ $value->itemName }}</td>
                    <td>{{ $value->jenisTruk }}</td>
                    <td>{{ date('d-m-Y H:i',strtotime($value->jam_in))  }}</td>
                    
                    
                    <td>
                    <a href="/cetaksegel/{{ $value->id }} " class="btn btn-primary" target="_blank" >cetak</a> 
                    </td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>
    <!-- AKHIR sudah segel -->
    




</div>
