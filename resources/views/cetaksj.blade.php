<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk SPM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <style>
        @media print {
  #print {
    display: none;
  }
}
    </style>
</head>

  <body onload="window.print()" >
    
@foreach ($struksj as $item)
    

    
                                                    <div class="container">
                                                        <a class="btn-primary" href='/lapsj' id="print">Back</a>
                                                        <div class="row">
                                                            <div class="col-sm-9">
                                                            <h6>   <b>NO SJ : {{ $item->spmNo }}</b>  </h6> 
                                                            <h6>   <b>  NO SO : {{ $item->sppbNo }}</b> </h6> 
                                                            <h3> <b>SURAT JALAN </b></h3> 
                                                            
                                                                                                         
                                                            </div>
                                                            <div class="col-sm" >
                                                            <h6>   <b>  Lamongan,  {{ date('d-m-Y ',strtotime($item->tglSpm)) }} </b></h6> 
                                                            <h6>   <b>   {{ $item->custName }} </b>  </h6>      
                                                            <h6>   <b>  {{ $item->custAdd }}</b> </h6>                                                  
                                                            </div>
                                                        </div> 
                                                      
                                                        <div class="form-group"><br>
                                                         
                                                         <div class="row">
                                                            <div class="col-sm-5">
                                                            <h5>   <b>    dengan kendaraan no. {{$item->carID}} </b></h5>
                                                                                                 
                                                            </div>
                                                            
                                                           
                                                        </div> 
                                                        
                                                        
                                                        <table border="1" style="width: 100%">
                                                            <thead >
                                                                    @php
                                                                          $no = 1;
                                                                        @endphp
                                                                        
                                                              <tr>
                                                                  
                                                                  <th class="border border-dark border-3 w-25 text-center"><h4><b>BANYAKNYA</h4></th>
                                                                    
                                                                  <th class="border border-dark border-3 text-center" colspan="2"><h4><b>NAMA BARANG</h4></th>
                                                                  {{-- <th class="border border-dark border-5"><h5><b>Keterangan</h5></th> --}}
                                                              </tr>
                                                              
                                                            </thead>
                                                            <tbody>

                                                              <tr>
                                                                
                                                                <th class="border border-dark border-3 w-25">
                                                                  <h5><b>{{$item->qtyKarung}} </b></h5>
                                                                </th>
                                                                
                                                                <th class="border border-dark border-3 w-2 m-auto"><h5><b> 
                                                                    @if ($item->packingID == 3) 
                                                                       Sak  
                                                                     @else 
                                                                       Pcs 
                                                                     </h5>
                                                                    @endif
                                                                </th>
                                                                <th class="border border-dark border-3">
                                                                  <h5><b>Gula Pasir</b></h5>
                                                                </th>
                                                              </tr>

                                                              <tr>
                                                                <th  class="border border-dark border-3">
                                                                  <h5><b> {{$item->qtyKg}} </b></h5>
                                                                </th>
                                                                <th class="border border-dark border-3">
                                                                  <h5><b>Kg</b></h5>
                                                                  
                                                                </th>
                                                                <th class="border border-dark border-3">
                                                                
                                                                  
                                                                </th>
                                                              </tr>

                                                              <tr>
                                                                <th class="border border-dark border-3" rowspan="4">

                                                                </th>
                                                                <th class="border border-dark border-3" rowspan="4">

                                                                </th>
                                                                <th class="border border-dark border-3 w-100">
                                                                  NO PO.    :   {{ $item->poNo }}
                                                                </th>
                                                              </tr>

                                                              <tr>
                                                                
                                                                <th class="border border-dark border-3 w-100">
                                                                  NO KONTRAK.    :   {{ $item->kontrakNo }}
                                                                </th>
                                                              </tr>
                                                              <tr>
                                                                
                                                                <th class="border border-dark border-3 w-100">
                                                                  NO SEAL.    :   {{ $item->sealNo }}
                                                                </th>
                                                              </tr>
                                                              <tr>
                                                                
                                                                <th class="border border-dark border-3 w-100">
                                                                  NO CONTAINER.    :   {{ $item->kontainerNo }}
                                                                </th>
                                                              </tr>
                                                            </tbody>
                                                        </table>
                                                        
                                                        <h6>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Note: Mohon Penerima mencantumkan nama jelas & Tgl terima barang </h6>
                                                          
                                                        
                                                        
                                                        </div>
                                                    </div>
         
    @endforeach
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
