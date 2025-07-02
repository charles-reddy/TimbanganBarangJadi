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
        <div wire:poll.5s>
            {{ now() }}
        </div>
        <div class="row">
            <div class="col">
                <div class="card m-auto   mt-3 text-white text-center bg-primary" style="max-width: 18rem;">
                    <h2>Approval Berat Rata2x </h2>
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
                        <label for="nama" class="col-sm-2 col-form-label" hidden>ID SPM</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="spmID" hidden>
                            <input type="text" class="form-control w-50" wire:model="spmNo" hidden>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Driver</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="driver" disabled>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="carID" class="col-sm-2 col-form-label">Car ID</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-25" wire:model="carID"  disabled>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="custID" class="col-sm-2 col-form-label">Customer</label>
                        <div class="col-sm-10">
                            
                            <input type="text" class="form-control w-50 mt-2" wire:model="custName" disabled>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="transpID" class="col-sm-2 col-form-label">Transporter</label>
                        <div class="col-sm-10" >
                            
                            <input type="text" class="form-control w-50 mt-2" wire:model="transpName" disabled>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="itemCode" class="col-sm-2 col-form-label">Item Code</label>
                        <div class="col-sm-10"  id="my-itemCode" wire:model="itemCode" disabled>
                               
                            <input type="text" class="form-control w-50 mt-2" wire:model="itemName" disabled>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="doNo" class="col-sm-2 col-form-label">DO No</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" wire:model="doNo" disabled>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Qty (Karung)</label>
                        <div class="col-sm-10">
                            <input type="text" id="input" class="form-control w-50" wire:model="b10QtyKarung" disabled >
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Rata Berat</label>
                        <div class="col-sm-10">
                            <input type="text" id="input" class="form-control w-50" wire:model="avgKarung" disabled >
                        </div>
                    </div>
                    


                </div>
               
            </div>

                    <div class="card text-center">
                        <div class="card-body offset-2">
                            <div class="mb-3 mt-3 row ">
                                            <div class="col-lg-4">
                                                    <div class="card border-primary mb-3" style="max-width: 150rem;">
                                                        <div class="card-header  text-center">Bukti Cek ulang 1</div>
                                                        <div class="card-body text-primary">
                                                            <img class="rounded float-start" style="width: 750px" src="{{ $buktiAppKarung1}}">
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="card border-primary mb-3" style="max-width: 150rem;">
                                                        <div class="card-header  text-center">Bukti Cek ulang 2</div>
                                                            <div class="card-body text-primary">
                                                                    <img class="rounded float-start" style="width: 750px" src="{{ $buktiAppKarung2}}"  >
                                                            </div>
                                                        </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="card border-primary mb-3" style="max-width: 150rem;">
                                                        <div class="card-header  text-center">Bukti Cek ulang 3</div>
                                                            <div class="card-body text-primary">
                                                                    <img class="rounded float-start" style="width: 750px" src="{{ $buktiAppKarung3}}"  >
                                                            </div>
                                                        </div>
                                            </div>

                                

                            </div>
                        </div>
                    </div>
            
           
            
            
            
          
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">
                    {{-- @if ($updateData == false)
                        <button type="button" class="btn btn-primary" name="submit" wire:click="store()">SIMPAN</button>
                    @else --}}
                        <button type="button" class="btn btn-primary" name="submit" wire:click="update()"  wire:confirm="Yakin simpan data?"  >APPROVE</button>
                    {{-- @endif --}}
                        <button type="button" class="btn btn-secondary" name="submit" wire:click="clear()">CLEAR</button>
                    
                </div>
            </div>
        </form>
    </div>
    <!-- AKHIR FORM -->

    <!-- START DATA Timbangan masuk-->
    <div class="my-3 p-3 bg-body rounded shadow-sm"  >
        <h1>Data Timbang Masuk</h1>
        {{-- <div class="pb-3 pt-3">
            <input type="text" class="form-control mb-3 w-25" placeholder="Searching ... driver" wire:model.live="katakunci">
        </div> --}}

        @if ($trscaleSelectedID)
            {{-- <a wire:click="deleteConfirmation('')" class="btn btn-danger btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">Del {{ count($trscaleSelectedID) }}  Data</a>   --}}
            <a wire:click="delete()" wire:confirm="Yakin Hapus data?"  class="btn btn-danger btn-sm mb-3">{{ count($trscaleSelectedID) }}  Data</a>
        
        @endif

        {{ $datascale->links() }}
        <table class="table table-striped table-sortable">
            <thead>
                <tr>
                    <th></th>
                    <th class="col-md">No</th>
                    <th class="col-md-1 sort @if($sortColumn=='driver') {{ $sortDirection }}   @endif" wire:click="sort('driver')" >Driver</th>
                    <th class="col-md-1 sort desc @if($sortColumn=='carID') {{ $sortDirection }}   @endif" wire:click="sort('carID')" >Car ID</th>
                    <th class="col-md-1 sort desc @if($sortColumn=='custName') {{ $sortDirection }}   @endif" wire:click="sort('custName')" >Customer</th>
                    <th class="col-md-1 sort desc @if($sortColumn=='transpID') {{ $sortDirection }}   @endif" wire:click="sort('transpID')" >Avg</th>
                    <th class="col-md-1 sort desc @if($sortColumn=='itemCode') {{ $sortDirection }}   @endif" wire:click="sort('itemCode')" >Item Name </th>
                    <th class="col-md-1 sort desc @if($sortColumn=='timbangin') {{ $sortDirection }}   @endif" wire:click="sort('timbangin')" >Bobot IN </th>
                    <th class="col-md-2 sort desc @if($sortColumn=='jam_in') {{ $sortDirection }}   @endif" wire:click="sort('jam_in')" >Date IN </th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($datascale as $key => $value)
                <tr>
                    {{-- <td><input type="checkbox" wire:key="{{ $value->id }}" value="{{ $value->id }}" wire:model.live="trscaleSelectedID"></td> --}}
                    <td></td>
                    <td>{{ $datascale->firstItem() + $key }}</td>
                    <td>{{ $value->driver }}</td>
                    <td>{{ $value->carID }}</td>
                    <td>{{ $value->custName }}</td>
                    <td>{{ number_format($value->avgKarung,3) }}</td>
                    <td>{{ $value->itemName }}</td>
                    <td>{{ $value->timbangin }}</td>
                    <td>{{ date('d-m-Y H:i',strtotime($value->jam_in)) }}</td>
                    
                    <td>
                        <a wire:click="edit({{ $value->id }})" class="btn btn-primary btn-sm">Approval</a>
                        {{-- <a wire:click="deleteConfirmation({{ $value->id }})" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">Del</a> --}}
                    </td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>
    <!-- AKHIR DATA Timbangan masuk -->
    
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Konfirmasi Hapus Data</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            Yakin akan menghapus data ini?
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
            <button type="button" class="btn btn-primary" wire:click="delete()" data-bs-dismiss="modal">Ya</button>
            </div>
        </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();

            $('#my-custID').on('change',function(e) {
                var data = $('#my-custID').select2("val");
                @this.set('custID',data);
            })


            // $(document).ready(function() {
            //     $('#my-custID').select2({
            //         placeholder: "Seleccione el Género"
            //     }).prepend('<option selected=""></option>')
            //     $('#my-custID').on('change', function(e) {
            //         @this.set('custID', e.target.value);
            //     });
            // });

            $('#my-transpID').on('change',function(e) {
                var data = $('#my-transpID').select2("val");
                @this.set('transpID',data);
            })

            $('#my-isApp').on('change',function(e) {
                var data = $('#my-isApp').select2("val");
                @this.set('isApp',data);
            })

            $('#my-itemCode').on('change',function(e) {
                var data = $('#my-itemCode').select2("val");
                @this.set('itemCode',data);
            })

            
        });
        
    </script>


</div>
