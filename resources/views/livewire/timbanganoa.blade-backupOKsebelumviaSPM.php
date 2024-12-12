
<div > 
    
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
        {{-- @include('layouts.navbar') --}}
        <div wire:poll.5s>
            {{ now() }}
            </div>
        <div class="row">
        <div class="row">
            <div class="col">
                <div class="card m-auto   mt-3 text-white text-center bg-primary" style="max-width: 18rem;">
                    <h2>TIMBANG MASUK</h2>
                </div>
            </div>
        </div>
        <form>
            <div class="row">
                <div class="col">
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Driver</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="driver">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="carID" class="col-sm-2 col-form-label">Car ID</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-25" wire:model="carID">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="custID" class="col-sm-2 col-form-label">Customer</label>
                        <div class="col-sm-10" wire:ignore>
                            <select   class="js-example-basic-single w-50" id="my-custID"  wire:model="custID"  >
                                <option></option>
                                @foreach ($customer as $item)
                                    <option value="{{ $item->custID }}" selected >{{ $item->custName }}</option>
                                @endforeach
                                
                            </select>
                            {{-- <input type="text" class="form-control w-50 mt-2" wire:model="custName" disabled> --}}
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="transpID" class="col-sm-2 col-form-label">Transporter</label>
                        <div class="col-sm-10" wire:ignore>
                            <select class="js-example-basic-single w-50" id="my-transpID" wire:model="transpID">
                                <option></option>
                                @foreach ($transporter as $item)
                                    <option value="{{ $item->transpID }}">{{ $item->transpName }}</option>
                                @endforeach
                            </select>
                            {{-- <input type="text" class="form-control w-50 mt-2" wire:model="transpName" disabled> --}}
                        </div>
                    </div>  
                    <div class="mb-3 row">
                        <label for="itemCode" class="col-sm-2 col-form-label">Item Code</label>
                        <div class="col-sm-10" wire:ignore>
                            <select class="js-example-basic-single w-50"  id="my-itemCode" wire:model="itemCode">
                                <option></option>
                                @foreach ($product as $item)
                                    <option value="{{ $item->itemCode }}">{{ $item->itemName }}</option>
                                @endforeach
                            </select>
                            {{-- <input type="text" class="form-control w-50 mt-2" wire:model="itemName" disabled> --}}
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="doNo" class="col-sm-2 col-form-label">DO / SPPB No </label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" wire:model='doNo'>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="poNo" class="col-sm-2 col-form-label">po No</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" wire:model='poNo'>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="remarks" class="col-sm-2 col-form-label">Remarks</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" wire:model="remarks">
                        </div>
                    </div>


                </div>
                <div class="col">
                    <div class="card mt-3 " style="width: 25rem;">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm5">
                                    <div>
                                         <input  type="text" class="form-control w-50" wire:model="timbangin"  disabled >
                                    </div>
                                    <span ></span>

                                    <div class="col">
                                        <button type="button" class="btn btn-primary mt-2" wire:click.prevent="timbang()">Timbang</button>
                                    </div>
                                    <div class="col">
                                        <select  class="mb-2 mt-2"  wire:model="timbanganID"  >
                                            <option value="">---pilih timbangan---</option>
                                            @foreach ($timbangan as $item)
                                                <option value="{{ $item->timbanganID }}"  >{{ $item->timbanganNama }}</option>
                                            @endforeach
                                            
                                        </select>
                                    </div>
                                    
                                </div>
                                
                            </div>
                        </div>
                        
                                
                            
                           
                            

                        
                    </div>
                    
                </div>
            </div>
            
           
            
            
            
          
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">
                    @if ($updateData == false)
                        <button type="button" class="btn btn-primary" name="submit" wire:click="store()">SIMPAN</button>
                    @else
                        <button type="button" class="btn btn-primary" name="submit" wire:click="update()">UPDATE</button>
                    @endif
                        <button type="button" class="btn btn-secondary" name="submit" wire:click="clear()">CLEAR</button>
                    
                </div>
            </div>
        </form>
    </div>
    <!-- AKHIR FORM -->

    <!-- START DATA -->
    <div class="my-3 p-3 bg-body rounded shadow-sm"  >
        <h1>Data Timbang Masuk</h1>
        <div class="pb-3 pt-3">
            <input type="text" class="form-control mb-3 w-25" placeholder="Searching ..." wire:model.live="katakunci">
        </div>

        @if ($trscaleSelectedID)
            {{-- <a wire:click="deleteConfirmation('')" class="btn btn-danger btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">Del {{ count($trscaleSelectedID) }}  Data</a>   --}}
            <a wire:click="delete()" wire:confirm="Yakin Hapus data?"  class="btn btn-danger btn-sm mb-3">{{ count($trscaleSelectedID) }}  Data</a>
        @endif

        {{ $datascale->links() }}
        <table class="table table-striped table-sortable">
            <thead>
                <tr>
                    <th></th>
                    <th class="col-md-1">No</th>
                    <th class="col-md-2 sort @if($sortColumn=='driver') {{ $sortDirection }}   @endif" wire:click="sort('driver')" >Driver</th>
                    <th class="col-md-2 sort desc @if($sortColumn=='carID') {{ $sortDirection }}   @endif" wire:click="sort('carID')" >Car ID</th>
                    <th class="col-md-2 sort desc @if($sortColumn=='custName') {{ $sortDirection }}   @endif" wire:click="sort('custName')" >Customer</th>
                    <th class="col-md-2 sort desc @if($sortColumn=='transpID') {{ $sortDirection }}   @endif" wire:click="sort('transpID')" >Transporter</th>
                    <th class="col-md-2 sort desc @if($sortColumn=='itemCode') {{ $sortDirection }}   @endif" wire:click="sort('itemCode')" >Item Name </th>
                    <th class="col-md-2 sort desc @if($sortColumn=='timbangin') {{ $sortDirection }}   @endif" wire:click="sort('timbangin')" >Bobot IN </th>
                    
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
                    <td>{{ $value->transpName }}</td>
                    <td>{{ $value->itemName }}</td>
                    <td>{{ $value->timbangin }}</td>
                    
                    <td>
                        {{-- <a wire:click="edit({{ $value->id }})" class="btn btn-warning btn-sm">Edit</a>
                        <a wire:click="deleteConfirmation({{ $value->id }})" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">Del</a> --}}
                    </td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
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

                $('#my-itemCode').on('change',function(e) {
                    var data = $('#my-itemCode').select2("val");
                    @this.set('itemCode',data);
                })

                
            });
            
        </script>
    </div>
    <!-- AKHIR DATA -->
    
 
  
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
</div>


