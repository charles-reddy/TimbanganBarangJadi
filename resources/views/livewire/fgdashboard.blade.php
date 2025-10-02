<div>
    <div class="container-fluid">
    
        
    
            <!-- <div class="col-sm-4 ms-2">
                <label for="">Filter by Date</label>
                <input type="date" id="tglin" class="form-control  mb-3 w-50"  wire:model.live="tglin">
            </div> -->
            <!-- Row atas tonase dan rendemen -->
            <div class="row" >
            <div class="col-md-auto mt-2">
                
                    <div class="card bg-warning" style="width: 12rem;" >
                        <div class="card-body">
                            <h1 class="card-title text-center ">

                            @php
                                if($antrianbsk){
                                    echo $antrianbsk->antrian;

                                } else {
                                    echo '0';
                                } 
                                @endphp
                                        
                                <h6 class="card-subtitle mb-2 text-muted"></h6>
                            <h5 class="card-text text-center ">
                                <a href = "/cardantrianbesok" class="text-white">Antrian Besok <h3>
                            </h5>
                            <a href="#" class="card-link"></a>
                            <a href="#" class="card-link"></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-auto mt-2">
                    <div class="card bg-info" style="width: 12rem;" >
                        <div class="card-body">
                            <h1 class="card-title text-center ">
                                @php
                                    if($antrianskr){
                                        echo $antrianskr->antrian;

                                    } else {
                                        echo '0';
                                    } 
                                @endphp
                           
                                        
                                <h6 class="card-subtitle mb-2 text-muted"></h6>
                                <h5 class="card-text text-center ">
                                    <a href = "/cardantrianhariini" class="text-white">Antrian Hari Ini <h3>
                                </h5>
                            <a href="#" class="card-link"></a>
                            <a href="#" class="card-link"></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-auto mt-2 ">
                    <div class="card bg-secondary " style="width: 12rem;">
                        <div class="card-body">
                            <h1 class="card-title text-center ">
                            {{ $registered}}
                                        
                                <h6 class="card-subtitle mb-2 text-muted"></h6>
                                <h5 class="card-text text-center ">
                                    <a href = "/cardregistered" class="text-white">Registrasi <h3>
                                </h5>
                            <a href="#" class="card-link"></a>
                            <a href="#" class="card-link"></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-auto mt-2 ">
                    <div class="card bg-primary " style="width: 12rem;">
                        <div class="card-body">
                            <h1 class="card-title text-center ">
                            {{ $datafgtruk->timIn}}
                                        
                                <h6 class="card-subtitle mb-2 text-muted"></h6>
                                <h5 class="card-text text-center ">
                                    <a href = "/cardwbin" class="text-white">Timb. Masuk <h3>
                                </h5>
                            <a href="#" class="card-link"></a>
                            <a href="#" class="card-link"></a>
                        </div>
                    </div>
                </div>
                

                <div class="col-md-auto mt-2">
                    <div class="card bg-info " style="width: 12rem;">
                        <div class="card-body">
                            <h1 class="card-title text-center">

                            {{ $datafgtruk->loading}}
                             </h1>
                            <h6 class="card-subtitle mb-2 text-muted"></h6>
                            <h5 class="card-text text-center ">
                                    <a href = "/cardloading" class="text-white">Sedang Muat <h3>
                                </h5>
                            <a href="#" class="card-link"></a>
                            <a href="#" class="card-link"></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-auto mt-2">
                    <div class="card bg-danger " style="width: 12rem;">
                        <div class="card-body">
                            <h1 class="card-title text-center">

                            {{ $datafgtruk->appavg}}
                             </h1>
                            <h6 class="card-subtitle mb-2 text-muted"></h6>
                            <h5 class="card-text text-center ">
                                    <a href = "/cardabnormal" class="text-white">Avg Abnormal <h3>
                                </h5>
                            <a href="#" class="card-link"></a>
                            <a href="#" class="card-link"></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-auto mt-2 mr-2">
                    <div class="card bg-success " style="width: 12rem;">
                        <div class="card-body">
                            <h1 class="card-title text-center ">
                            {{ $datafgtruk->timout}}
                            </h1>
                            <h6 class="card-subtitle mb-2 text-muted"></h6>
                            
                            <h5 class="card-text text-center ">
                                <a href = "/cardwbout" class="text-white">Timb. Keluar <h3>
                            </h5>
                            <a href="#" class="card-link"></a>
                            <a href="#" class="card-link"></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-auto mt-2">
                    <div class="card bg-primary " style="width: 12rem;">
                        <div class="card-body">
                            <h1 class="card-title text-center ">
                            {{ $datafgtruk->pgi}}
                            </h1>
                            <h6 class="card-subtitle mb-2 text-muted"></h6>
                            
                            <h5 class="card-text text-center ">
                                <a href = "/cardpgi" class="text-white">Sudah PGI <h3>
                            </h5>
                            <a href="#" class="card-link"></a>
                            <a href="#" class="card-link"></a>
                        </div>
                    </div>
                </div>

                
            </div>
            <!-- Row atas tonase dan rendemen -->
            
            <div class="row mt-4">
                <div class="col-sm-6">
                    <div class="card ">
                        <div class="card-header border-0 bg-primary">
                                    <h2 style="text-align:center;"><b>Delivery Summary - 
                                        <div wire:poll.60s >
                                                <!-- {{ now() }} -->
                                                {{ $datafgtruk->tgl}}
                                            
                                        </div>
                                  
                                        </b></h2>
                                
                                    
                                    
                                
                        </div>
            <!-- Delivery Summary -->
                        <!-- awal summary-->
                         <div class="card">
                            <div class="card-body table-responsive p-0">
                                    <table class="table table-striped  ">
                                        <thead>
                                            <tr>
                                                <th class="col-md-1" ><h2><b>Item Name</b></h2></th>
                                                <th class="col-md-1" ><h2><b>Truck(s)</b></h2></th>
                                                <th class="col-md-1" ><h2><b>MT</b></h2></th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="col-md-1" ><h4><b>GKP - Kemasan 65 Gram</b></h4></td>
                                                <td class="col-md-1" ><h4><b>{{ $datafgtruk->trukGKP65 }}</b></h4></td>
                                                <td class="col-md-1" ><h4><b> 
                                                    @php
                                                        if($datafgtruk->netGKP65) {
                                                            echo number_format($datafgtruk->netGKP65/1000,2);
                                                        } else {
                                                            echo number_format(0,2);
                                                        }

                                                    @endphp
                                                </b></h4></td>
                                            </tr>
                                            <tr>
                                                <td class="col-md-1" ><h4><b>GKP - Kemasan 500 Gram</b></h4></td>
                                                <td class="col-md-1" ><h4><b>{{ $datafgtruk->trukGKP500g }}</b></h4></td>
                                                <td class="col-md-1" ><h4><b> 
                                                    @php
                                                        if($datafgtruk->netGKP500g) {
                                                            echo number_format($datafgtruk->netGKP500g/1000,2);
                                                        } else {
                                                            echo number_format(0,2);
                                                        }

                                                    @endphp
                                                </b></h4></td>
                                            </tr>

                                            <tr>
                                                <td class="col-md-1" ><h4><b>GKP - Kemasan 1 Kg</b></h4></td>
                                                <td class="col-md-1" ><h4><b>{{ $datafgtruk->trukGKP1kg }}</b></h4></td>
                                                <td class="col-md-1" ><h4><b> 
                                                    @php
                                                        if($datafgtruk->netGKP1kg) {
                                                            echo number_format($datafgtruk->netGKP1kg/1000,2);
                                                        } else {
                                                            echo number_format(0,2);
                                                        }

                                                    @endphp
                                                </b></h4></td>
                                            </tr>

                                            <tr>
                                                <td class="col-md-1" ><h4><b>GKP - Kemasan 50 Kg</b></h4></td>
                                                <td class="col-md-1" ><h4><b>{{ $datafgtruk->trukGKP50Kg }}</b></h4></td>
                                                <td class="col-md-1" ><h4><b> 
                                                    @php
                                                        if($datafgtruk->netGKP50Kg) {
                                                            echo number_format($datafgtruk->netGKP50Kg/1000,2);
                                                        } else {
                                                            echo number_format(0,2);
                                                        }

                                                    @endphp
                                                </b></h4></td>
                                            </tr>

                                            <tr>
                                                <td class="col-md-1" ><h4><b>GKP - BULK Unpacking</b></h4></td>
                                                <td class="col-md-1" ><h4><b>{{ $datafgtruk->trukGKPbulk }}</b></h4></td>
                                                <td class="col-md-1" ><h4><b> 
                                                    @php
                                                        if($datafgtruk->netGKPbulk) {
                                                            echo number_format($datafgtruk->netGKPbulk/1000,2);
                                                        } else {
                                                            echo number_format(0,2);
                                                        }

                                                    @endphp
                                                </b></h4></td>
                                            </tr>

                                            <tr>
                                                <td class="col-md-1" ><h4><b>GKP - Molases</b></h4></td>
                                                <td class="col-md-1" ><h4><b>{{ $datafgtruk->trukGKPMol }}</b></h4></td>
                                                <td class="col-md-1" ><h4><b> 
                                                    @php
                                                        if($datafgtruk->netGKPMol) {
                                                            echo number_format($datafgtruk->netGKPMol/1000,2);
                                                        } else {
                                                            echo number_format(0,2);
                                                        }

                                                    @endphp
                                                </b></h4></td>
                                            </tr>

                                            <tr>
                                                <td class="col-md-1" ><h4><b>GKR - Premium 50Kg</b></h4></td>
                                                <td class="col-md-1" ><h4><b>{{ $datafgtruk->trukGKR50Kg }}</b></h4></td>
                                                <td class="col-md-1" ><h4><b> 
                                                    @php
                                                        if($datafgtruk->netGKR50Kg) {
                                                            echo number_format($datafgtruk->netGKR50Kg/1000,2);
                                                        } else {
                                                            echo number_format(0,2);
                                                        }

                                                    @endphp
                                                </b></h4></td>
                                            </tr>

                                            <tr>
                                                <td class="col-md-1" ><h4><b>GKR - BULK Unpacking</b></h4></td>
                                                <td class="col-md-1" ><h4><b>{{ $datafgtruk->trukGKRbulk }}</b></h4></td>
                                                <td class="col-md-1" ><h4><b> 
                                                    @php
                                                        if($datafgtruk->netGKRbulk) {
                                                            echo number_format($datafgtruk->netGKRbulk/1000,2);
                                                        } else {
                                                            echo number_format(0,2);
                                                        }

                                                    @endphp
                                                </b></h4></td>
                                            </tr>

                                            <tr>
                                                <td class="col-md-1" ><h4><b>GKR - Molases</b></h4></td>
                                                <td class="col-md-1" ><h4><b>{{ $datafgtruk->trukGKRMol }}</b></h4></td>
                                                <td class="col-md-1" ><h4><b> 
                                                    @php
                                                        if($datafgtruk->netGKRMol) {
                                                            echo number_format($datafgtruk->netGKRMol/1000,2);
                                                        } else {
                                                            echo number_format(0,2);
                                                        }

                                                    @endphp
                                                </b></h4></td>
                                            </tr>
                                        
                                            
                                        </tbody> 
                                    </table>
                                
                            </div>
                        </div>
                        <!-- akhir summary-->

                        
                    </div>
                </div>
                    <!-- awal chart 7 hari delivery -->
                    <div class="col-sm-6">
                        <div class="card">
                            <canvas  id="myChart" height="665px" ></canvas>
                        </div>
                    </div>
        <!-- akhir chart 7 hari delivery -->

            </div>
            <!-- Delivery Summary -->


    </div>
    
        
            <div class="row mt-4">

        <!-- awal detail truk out-->
            <div class="col-sm-6">
                        
                            
                        <div class="card-header border-0 bg-primary">
                            <br>
                            <h1 style="text-align:center;"> Delivery Details ( Today )</h1>  <br>                     
                        </div>
                        
                        

                        

                        {{ $datatrukout->links() }}
                        <table class="table table-striped table-sortable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="col-md">No</th>
                                    <th class="col-md" >Tiket Muat</th>
                                    
                                    <th class="col-md" >SPM NO</th>
                                    <th class="col-md" >Driver</th>
                                    <th class="col-md" >Car ID</th>
                                    <th class="col-md" >Customer</th>
                                    <th class="col-md" >Item</th>
                                    <th class="col-md" >WB IN</th>
                                    <th class="col-md" >WB Out</th>
                                    <th class="col-md" >Netto</th>
                                    
                                    
                                    
                                    
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datatrukout as $key => $value)
                                <tr>
                                    <td></td>
                                    <td>{{ $datatrukout->firstItem() + $key }}</td>
                                    <td>{{ $value->pendfNo }}</td>
                                    
                                    <td>{{ $value->spmNo }}</td>
                                    <td>{{ $value->driver }}</td>
                                    <td>{{ $value->carID }}</td>
                                    <td>{{ $value->custName }}</td>
                                    <td>{{ $value->itemName }}</td>
                                    <td>{{ $value->timbangin }}</td>
                                    <td>{{ $value->timbangout }}</td>
                                    <td>{{ $value->netto }}</td>
                                    
                                    
                                    
                                    <!-- <td>
                                        <a wire:click="edit({{ $value->id }})" class="btn btn-primary btn-sm">Seal</a>
                                    </td> -->
                                </tr>
                                @endforeach
                                
                            </tbody>
                        </table>
                    
                
            </div>
        <!-- akhir detail truk out-->

        <!-- awal 7 hari jumlah truk-->
            <div class="col-sm-6">
                        <div class="card">
                            <canvas  id="myChart1" height="665px" ></canvas>
                        </div>
            </div>
        <!-- akhir 7 hari jumlah truk-->

        <!-- awal 7 hari antrian truk-->
        <div class="my-3 p-3 bg-body rounded shadow-sm"  >
        <h1> Antrian 7 Hari ke depan</h1>
        <div class="row">
                <div class="col-sm-4">
                <!-- <label for=""></label>
                    <input type="text" class="form-control mb-3 w-25" placeholder="Searching ... Plat no" wire:model.live="katakunci">
                </div> -->
                <!-- <div class="col-sm-2 ms-2">
                        <label for="">Filter by Date IN</label>
                        <input type="date" id="tglmuat" class="form-control  mb-3 w-50"  wire:model.live="tglmuat">
                </div> -->
        
            </div>
            {{ $data7hari->links() }}
            <table class="table table-striped table-sortable">
                <thead>
                    <tr>
                        <th></th>
                        <th class="col-md">No</th>
                        <th class="col-md" >Tiket Muat</th>
                        <th class="col-md" >SPPB No</th>
                        <th class="col-md" >Driver</th>
                        <th class="col-md" >Car ID</th>
                        <th class="col-md" >Customer</th>
                        <th class="col-md" >Item </th>
                        <th class="col-md" >Truck Type </th>
                        <th class="col-md" >Weight (KG) </th>
                        <th class="col-md" >Tgl Muat </th>
                    
                        
                        
                        
                        
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data7hari as $key => $value)
                    <tr>
                        <td></td>
                        <td>{{ $data7hari->firstItem() + $key }}</td>
                        <td>{{ $value->pendfNo }}</td>
                        <td>{{ $value->sppbNo }}</td>
                        <td>{{ $value->tmDriver }}</td>
                        <td>{{ $value->tmCarID }}</td>
                        <td>{{ $value->custName }}</td>
                        <td>{{ $value->itemName }}</td>
                        <td>{{ $value->jenisTruk }}</td>
                        <td>{{ $value->tmQtyKg }}</td>
                        <td>{{ $value->tglMuat }}</td>
                        
                        
                    </tr>
                    @endforeach
                    
                </tbody>
            </table>
        </div>
        <!-- akhir 7 hari antrian truk-->
        
    </div>
        
    
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.2/chart.min.js" integrity="sha512-zjlf0U0eJmSo1Le4/zcZI51ks5SjuQXkU0yOdsOBubjSmio9iCUp8XPLkEAADZNBdR9crRy3cniZ65LF2w8sRA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <script>
            var chartData = JSON.parse(`<?php echo $transac ?>`);
            // console.log(chartData);
            const ctx = document.getElementById('myChart');
            new Chart(ctx, {
                type: 'bar',
                data: {
                labels: chartData.label,
                datasets: [{
                    label: '7 DAYS DELIVERY GRAPHIC ( KG )',
                    data: chartData.data,
                    borderWidth: 1,
                    backgroundColor: ['lightgreen'],
                }]
                },
                options: {
                scales: {
                    y: {
                    beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                family: 'Arial',
                                size: 23,      
                                style: 'normal',
                                weight: 'bold',  
                                lineHeight: 1.2, 
                            },
                        },
                    },
                },
                }
            });
        </script>
    
    <script>
            var chartDatajmltruk = JSON.parse(`<?php echo $jmltruk ?>`);
            // console.log(chartData1);
            const ctxjmltruk = document.getElementById('myChart1');
            new Chart(ctxjmltruk, {
                type: 'line',
                data: {
                labels: chartDatajmltruk.label,
                datasets: [{
                    label: '7 DAYS DELIVERY GRAPHIC ( TRUCKS )',
                    data: chartDatajmltruk.data,
                    borderWidth: 3,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: ['blue','yellow','green', 'pink', 'oranga', 'black', 'magenta'],
                }]
                },
                options: {
                scales: {
                    y: {
                    beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                family: 'Arial',
                                size: 23,      
                                style: 'normal',
                                weight: 'bold',  
                                lineHeight: 1.2, 
                            },
                        },
                    },
                },
                }
            });
        </script>
</div>
