<div>
     <div>
        <h3> Sudah di Cek Security</h3>
    
    </div>
        <div class="card-body table-responsive p-0">
            {{ $tmsdhdatang->links() }}
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
                        <th class="col-md" >Waktu Security Approve</th>
                        
                        
                        
                        
                        
                        
                        
                        
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalkg = 0;
                    @endphp
                    @foreach ($tmsdhdatang as $key => $value)
                    <tr>
                        <td></td>
                        <td>{{ $tmsdhdatang->firstItem() + $key }}</td>
                        <td>{{ $value->pendfNo }}</td>
                        <td>{{ $value->tmDriver }}</td>
                        <td>{{ $value->tmCarID }}</td>
                        <td>{{ $value->tglMuat }}</td>
                        <td>{{ $value->custName }}</td>
                        <td>{{ $value->isSecCekDate }}</td>
                        
                        
                       
                    </tr>
                    @endforeach
                    
                </tbody>
            </table>
            
        </div>  {{-- In work, do what you enjoy. --}}

        <hr> <br>

        <div>
        <h3> Sudah Registrasi / dibuatkan SPM</h3>
    
    </div>
        <div class="card-body table-responsive p-0">
            {{ $registered->links() }}
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
                        <th class="col-md" >Waktu Security Approve</th>
                        
                        
                        
                        
                        
                        
                        
                        
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalkg = 0;
                    @endphp
                    @foreach ($registered as $key => $value)
                    <tr>
                        <td></td>
                        <td>{{ $registered->firstItem() + $key }}</td>
                        <td>{{ $value->spmNo }}</td>
                        <td>{{ $value->pendfNo }}</td>
                        <td>{{ $value->driver }}</td>
                        <td>{{ $value->carID }}</td>
                        <td>{{ $value->tglMuat }}</td>
                        <td>{{ $value->custName }}</td>
                        <td>{{ $value->isSecCekDate }}</td>
                        
                        
                       
                    </tr>
                    @endforeach
                    
                </tbody>
            </table>
            
        </div>  {{-- In work, do what you enjoy. --}}




        {{-- ======================================== --}}


</div>
