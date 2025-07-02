<div>
    <!-- START DATA Timbangan masuk-->
    <div class="my-3 p-3 bg-body rounded shadow-sm"  >
        <h1>Data Surat Jalan </h1>
        <div class="row">
            <div class="col-sm-4" > 
                <label for="">Search</label>
                <input type="text" id="katakunciout" class="form-control mr-1 mb-3 w-50" placeholder="Type Driver or Car ID" wire:model.live="katakunciout">
                
            </div>
            <div class="col-sm-4 ms-2">
                <label for="">Filter by Date IN</label>
                <input type="date" id="tglin" class="form-control  mb-3 w-50"  wire:model.live="tglin">
            </div>
            <div>
                <button type="button" class="btn btn-primary" wire:click="clear()">Clear </button>
                {{-- <button type="button" class="btn-primary" wire:click="export_out()">Export</button>  --}}
            </div>
        </div> 

        @if ($trscaleSelectedID)
            {{-- <a wire:click="deleteConfirmation('')" class="btn btn-danger btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">Del {{ count($trscaleSelectedID) }}  Data</a>   --}}
            <a wire:click="delete()" wire:confirm="Yakin Hapus data?"  class="btn btn-danger btn-sm mb-3">{{ count($trscaleSelectedID) }}  Data</a>
        
        @endif

        {{ $datascaleout->links() }}
        <table class="table table-striped table-sortable">
            <thead>
                <tr>
                    <th></th>
                    <th class="col-md">No</th>
                    <th class="col-md-1 sort @if($sortColumn=='driver') {{ $sortDirection }}   @endif" wire:click="sort('driver')" >Driver</th>
                    <th class="col-md-1 sort desc @if($sortColumn=='carID') {{ $sortDirection }}   @endif" wire:click="sort('carID')" >Car ID</th>
                    <th class="col-md-2 sort desc @if($sortColumn=='custName') {{ $sortDirection }}   @endif" wire:click="sort('custName')" >Customer</th>
                    <th class="col-md-1 sort desc @if($sortColumn=='qtyKg') {{ $sortDirection }}   @endif" wire:click="sort('itemName')" >Barang </th>
                    <th class="col-md-1 sort desc @if($sortColumn=='qtyKg') {{ $sortDirection }}   @endif" wire:click="sort('qtyKg')" >Bobot SPM </th>
                    <th class="col-md-2 sort desc @if($sortColumn=='spmNo') {{ $sortDirection }}   @endif" wire:click="sort('spmNo')" >SJ No </th>
                    <th class="col-md-2 sort desc @if($sortColumn=='sppbNo') {{ $sortDirection }}   @endif" wire:click="sort('sppbNo')" >SPPB No </th>
                    <th class="col-md-1 sort desc @if($sortColumn=='tglSpm') {{ $sortDirection }}   @endif" wire:click="sort('tglSpm')" >Tgl SPM </th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($datascaleout as $key => $value)
                <tr>
                    {{-- <td><input type="checkbox" wire:key="{{ $value->id }}" value="{{ $value->id }}" wire:model.live="trscaleSelectedID"></td> --}}
                    <td></td>
                    <td>{{ $datascaleout->firstItem() + $key }}</td>
                    <td>{{ $value->driver }}</td>
                    <td>{{ $value->carID }}</td>
                    <td>{{ $value->custName }}</td>
                    <td>{{ $value->itemName }}</td>
                    <td>{{ $value->qtyKg }}</td>
                    <td>{{ $value->spmNo }}</td>
                    <td>{{ $value->sppbNo }}</td>
                    <td>{{ date('d-m-Y ',strtotime($value->tglSpm)) }}</td>
                   
                        {{-- <a wire:click="edit({{ $value->id }})" class="btn btn-primary btn-sm">Timbang Keluar</a> --}}
                        {{-- <a wire:click="deleteConfirmation({{ $value->id }})" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">Del</a> --}}
                    </td>
                    <td>
                        <a href="/cetaksj/{{ $value->id }} " class="btn btn-primary" target="_blank" >Cetak Surat Jalan</a>
                    </td>
                </tr>
                @endforeach
                
            </tbody> 
        </table>
    </div>
    <!-- AKHIR DATA Timbangan masuk --> 
</div>
