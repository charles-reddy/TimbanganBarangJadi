<div>
    <!-- START data out-->
    <div class="my-3 p-3 bg-body rounded shadow-sm"  >
        <h1> Truk Sudah Timbang Masuk</h1>
        <div class="row">
                <div class="col-sm-4">
                <label for=""></label>
                    <input type="text" class="form-control mb-3 w-25" placeholder="Searching ... Plat no" wire:model.live="katakunci">
                </div>
                <!-- <div class="col-sm-2 ms-2">
                        <label for="">Filter by Date Out</label>
                        <input type="date" id="tglout" class="form-control  mb-3 w-50"  wire:model.live="tglout">
                </div> -->
        
        </div>
        <div class="card-body table-responsive p-0">
            {{ $datain->links() }}
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
                        <th class="col-md" >Date Out </th>
                        <th class="col-md" >Car</th>
                        
                        
                        
                        
                        
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datain as $key => $value)
                    <tr>
                        <td></td>
                        <td>{{ $datain->firstItem() + $key }}</td>
                        <td>{{ $value->spmNo }}</td>
                        <td>{{ $value->driver }}</td>
                        <td>{{ $value->carID }}</td>
                        <td>{{ $value->custName }}</td>
                        <td>{{ $value->itemName }}</td>
                        <td>{{ $value->jenisTruk }}</td>
                        <td>{{ date('d-m-Y H:i',strtotime($value->jam_in))  }}</td>
                        <td>{{ date('d-m-Y H:i',strtotime($value->jam_out))  }}</td>
                        <td>{{ number_format($value->timbangin) }}</td>
                        
                        
                        
                        
                    
                    </tr>
                    @endforeach
                    
                </tbody>
            </table>
        </div>
    </div>
    <!-- AKHIR data ind -->
</div>
