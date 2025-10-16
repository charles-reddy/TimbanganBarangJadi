<div>
    <div>
        <h3> Sudah Registrasi / Belum Timbang Masuk</h3>
    
    </div>
        <div class="card-body table-responsive p-0">
            {{ $registrasikmrblmmasuk->links() }}
            <table class="table table-striped table-sortable">
                <thead>
                    <tr>
                        <th></th>
                        <th class="col-md">No</th>
                        <th class="col-md" >SPM</th>
                        <th class="col-md" >Tiket Muat</th>
                        <th class="col-md" >Sopir</th>
                        <th class="col-md" >Plat No</th>
                        <th class="col-md" >Tgl Muat</th>
                        <th class="col-md" >Customer</th>
                        
                        
                        
                        
                        
                        
                        
                        
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalkg = 0;
                    @endphp
                    @foreach ($registrasikmrblmmasuk as $key => $value)
                    <tr>
                        <td></td>
                        <td>{{ $registrasikmrblmmasuk->firstItem() + $key }}</td>
                        <td>{{ $value->spmNo }}</td>
                        <td>{{ $value->pendfNo }}</td>
                        <td>{{ $value->driver }}</td>
                        <td>{{ $value->carID }}</td>
                        <td>{{ $value->tglMuat }}</td>
                        <td>{{ $value->custName }}</td>
                        
                        
                       
                    </tr>
                    @endforeach
                    
                </tbody>
            </table>
            
        </div>  {{-- In work, do what you enjoy. --}}

        <hr> <br>

        <div>
        <h3> Belum Loading</h3>
    
    </div>
        <div class="card-body table-responsive p-0">
            {{ $timbanginkmrblmkeluar->links() }}
            <table class="table table-striped table-sortable">
                <thead>
                    <tr>
                        <th></th>
                        <th class="col-md">No</th>
                        <th class="col-md" >SPM</th>
                        <th class="col-md" >Tiket Muat</th>
                        <th class="col-md" >Sopir</th>
                        <th class="col-md" >Plat No</th>
                        <th class="col-md" >Tgl Muat</th>
                        <th class="col-md" >Customer</th>
                        
                        
                        
                        
                        
                        
                        
                        
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalkg = 0;
                    @endphp
                    @foreach ($timbanginkmrblmkeluar as $key => $value)
                    <tr>
                        <td></td>
                        <td>{{ $timbanginkmrblmkeluar->firstItem() + $key }}</td>
                        <td>{{ $value->spmNo }}</td>
                        <td>{{ $value->pendfNo }}</td>
                        <td>{{ $value->driver }}</td>
                        <td>{{ $value->carID }}</td>
                        <td>{{ $value->tglMuat }}</td>
                        <td>{{ $value->custName }}</td>
                        
                        
                       
                    </tr>
                    @endforeach
                    
                </tbody>
            </table>
            
        </div>  {{-- In work, do what you enjoy. --}}


{{-- ======================================== --}}

<hr> <br>

        <div>
        <h3> Tidak Datang</h3>
    
    </div>
        <div class="card-body table-responsive p-0">
            {{ $tidakdatang->links() }}
            <table class="table table-striped table-sortable">
                <thead>
                    <tr>
                        <th></th>
                        <th class="col-md">No</th>
                        <th class="col-md" >Tiket Muat</th>
                        <th class="col-md" >Sopir</th>
                        <th class="col-md" >Plat No</th>
                        <th class="col-md" >Tgl Muat</th>
                        <th class="col-md" >Customer</th>

                        
                        
                        
                        
                        
                        
                        
                        
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalkg = 0;
                    @endphp
                    @foreach ($tidakdatang as $key => $value)
                    <tr>
                        <td></td>
                        <td>{{ $tidakdatang->firstItem() + $key }}</td>
                        <td>{{ $value->pendfNo }}</td>
                        <td>{{ $value->tmDriver }}</td>
                        <td>{{ $value->tmCarID }}</td>
                        <td>{{ $value->tglMuat }}</td>
                        <td>{{ $value->custName }}</td>
                        
                        
                       
                    </tr>
                    @endforeach
                    
                </tbody>
            </table>
            
        </div>  {{-- In work, do what you enjoy. --}}


        
</div>
