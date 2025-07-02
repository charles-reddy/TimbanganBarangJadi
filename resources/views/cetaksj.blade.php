<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk Surat Jalan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <style>
        @media print {
          #print {
            display: none;
          }
        }

        table tr {
          font-family: sans-serif;
          border: 0.03ch solid black;
          border-collapse: collapse;
          height: 10%;

        }
 
        table td {
          font-family: sans-serif;
          border: 0.03ch solid black;
          border-collapse: collapse;
          height: 10%;

        }
    </style>
</head>

  <body onload="window.print()" >
    
@foreach ($struksj as $item)
    

    
                                                    <div class="container">
                                                        <a class="btn-primary" href='/lapsj' id="print">Back</a>
                                                        <div class="row">
                                                            <div class="col-sm-9">
                                                            <h6>   <b>NO SJ : {{ $item->spmNo }}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                              &nbsp;  Lamongan,  {{ date('d-m-Y ',strtotime($item->tglSpm)) }}</b>  </h6> 
                                                            <h6>   <b>  NO SO : {{ $item->sppbNo }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                              &nbsp;   {{ $item->custName }}</b> </h6> 
                                                              <h6> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                &nbsp;&nbsp;&nbsp;&nbsp;  <b>  {{ $item->custAdd }}</b> </h6>   
                                                            <h5> <b>SURAT JALAN </b></h5> 
                                                            
                                                                                                         
                                                            </div>
                                                            
                                                        </div> 
                                                      
                                                        <div>
                                                         
                                                         <div class="row"> 
                                                            <div class="col-sm-5">
                                                            <h5>   <b>    dengan kendaraan no. {{$item->carID}} </b></h5>
                                                                                                 
                                                            </div>
                                                            
                                                           
                                                        </div> 
                                                        
                                                        
                                                        <table border="1" style="width: 100%">
                                                            
                                                                    @php
                                                                          $no = 1;
                                                                        @endphp
                                                                        
                                                              <tr>
                                                                  
                                                                  <td class=" w-25 text-center"><h5>BANYAKNYA</h5></td>
                                                                    
                                                                  <td class="text-center" colspan="2"><h5>NAMA BARANG</h5></td>
                                                              </tr>
                                                              
                                                            
                                                            <tbody>

                                                              <tr>
                                                                
                                                                <td class=" text-center w-25"  >
                                                                  <h5><b>{{number_format($item->qtyKarung)}} </b></h5>
                                                                  
                                                                </td>
                                                                
                                                                <td class="  w-25 text-center m-auto" ><h5><b> 
                                                                    @if ($item->packingID == 3) 
                                                                       Sak  
                                                                     @else 
                                                                       Pcs 
                                                                     </h5>
                                                                    @endif
                                                                </td>
                                                                <td class=" ">
                                                                  <h5><b>&nbsp;
                                                                  @if($item->type == 'FG-L') 
                                                                    Molasses
                                                                  @else 
                                                                    Gula Pasir
                                                                  @endif 
                                                                    
                                                                  
                                                                  </b></h5>
                                                                </td>
                                                              </tr>

                                                              <tr>
                                                                <td  class=" text-center "  >
                                                                  <h5><b> {{number_format($item->qtyKg)}} </b></h5>
                                                                </td>
                                                                <td class="  text-center" >
                                                                  <h5><b>Kg</b></h5>
                                                                  
                                                                </td>
                                                                <td>

                                                                </td>
                                                                
                                                              </tr>

                                                              <tr>
                                                                <td class=" " rowspan="4">
                                                                    
                                                                </td>
                                                                <td class=" " rowspan="4">

                                                                </td>
                                                                <td class="w-100" style="height: 80px" >
                                                                 <h5> &nbsp;NO PO.    :   {{ $item->poNo }}</h5><br>
                                                                 <h5>&nbsp;NO KONTRAK.    :   {{ $item->kontrakNo }}</h5><br>
                                                                 <h5>&nbsp;NO SEAL.    :   {{ $item->sealNo }}</h5><br>
                                                                 <h5> &nbsp;NO CONTAINER.    :   {{ $item->kontainerNo }}</h5> <br>
                                                                </td>
                                                              </tr>

                                                             
                                                            </tbody>
                                                        </table>
                                                        <br>
                                                        <table border="1" style="width: 100%" class="text-center">
                                                          <tr>
                                                            <td>
                                                              <h4>Penerima</h4>
                                                              <br><br><br>
                                                              <h4>{{ $item->driver }}</h4>
                                                            </td>
                                                            <td>
                                                              <h4>Registrasi Barang Jadi</h4>
                                                              <br><br><br>
                                                              <h4>&nbsp;</h4>
                                                            </td>
                                                            <td>
                                                              <h4>Admin Gudang</h4>
                                                              <br><br><br>
                                                              <h4>&nbsp;</h4>
                                                              
                                                            </td>
                                                          </tr>
                                                          
                                                        </table>
                                                        
                                                        <h6>
                                                        Note: Mohon Penerima mencantumkan nama jelas & Tgl terima barang </h6>
                                                          
                                                        
                                                        
                                                        </div>
                                                    </div>
         
    @endforeach
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
