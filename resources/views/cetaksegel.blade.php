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

  <body onload="window.print()">
    
@foreach ($struksegel as $item)
    

    
                                                    <div class="container">
                                                        <a class="btn-primary" href='/createspm' id="print">Back</a>
                                                        <div class="row">
                                                            <div class="col-sm ">
                                                            <h4>   <b>PT Kebun Tebu Mas &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                              </b>  </h4> 
                                                            <h4>   <b>  Jl Raya Babat Jombang Km 25.5 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                              </b> </h4>  
                                                            <h4>   <b> Ds. Lamongrejo - Kec. Ngimbang</b>  </h4>                                               
                                                            </div>
                                                            
                                                        </div>
                                                        
                                                        
                                                      <br>
                                                        
                                                          <div class="row text-center">
                                                            <div class="col-sm-12">
                                                              <h5>FROM SEGEL TRUK</h5>
                                                                                                 
                                                            </div>
                                                            

                                                            <br>
                                                            
                                                           
                                                          </div> 

                                                          <div class="row">
                                                            <div class="col-sm-12">
                                                              <h1>Pada Hari ini, tgl {{date('d-m-Y',strtotime($item->tglSpm))}}, telah dilakukan penimbangan dan penyegelan truk dengan data sebagai berikut : </h1>
                                                              <ul>
                                                                <li>No Kendaraan :   {{$item->carID}}</li>
                                                                <li>Customer :   {{$item->custName}}</li>
                                                                <li>No SPPB :   {{$item->sppbNo}}</li>
                                                                <li>Nama Barang :   {{$item->itemName}}</li>
                                                                <li>Car Weight :   {{number_format($item->timbangin)}} Kg</li>
                                                                <li>Gross Weight :   {{number_format($item->timbangout)}} Kg</li>
                                                                <li>Net Weight :   {{number_format($item->netto)}} Kg</li>
                                                              </ul>
                                                            </div>
                                                          </div>

                                                          
                                                            
                                                            
                                                        </div>
                                                            
                                                        <table>
                                                          <tr>
                                                            <td class="text-center">Segel 1</td>
                                                            @if($item->fotoSealNo2)
                                                              <td class="text-center">Segel 2</td>
                                                            @else

                                                            @endif

                                                            @if($item->fotoSealNo3)
                                                              <td class="text-center">Segel 3</td>
                                                            @else

                                                            @endif

                                                            @if($item->fotoSealNo4)
                                                              <td class="text-center">Segel 4</td>
                                                            @else

                                                            @endif

                                                            @if($item->fotoSealNo5)
                                                              <td class="text-center">Segel 5</td>
                                                            @else

                                                            @endif
                                                            @if($item->imgFormLoading)
                                                            <td class="text-center">Krani : {{$item->krani}}</td>
                                                            @else

                                                            @endif
                                                          </tr>
                                                          
                                                          <tr>
                                                         
                                                            <td> @php
                                                                  $img = "storage/";
                                                                  $segel1 = $img . $item->fotoSealNo1;
                                                                  @endphp
                                                              <img class="rounded mx-auto d-block" style="width: 20%" src="{{$segel1}}"   >
                                                            </td>
                                                            @if($item->fotoSealNo2)
                                                                <td> @php
                                                                      $img = "storage/";
                                                                      if($item->fotoSealNo2)
                                                                      {
                                                                        $segel2 = $img . $item->fotoSealNo2;
                                                                      } else {
                                                                        $segel2 = 'storage/uploads/noimage.jpg';
                                                                      }
                                                                      
                                                                      @endphp
                                                                      

                                                                        <img class="rounded mx-auto d-block" style="width: 20%" src="{{$segel2}}"   >
                                                                    
                                                                </td>
                                                            @else
                                                                    
                                                            @endif

                                                            @if($item->fotoSealNo3)
                                                            <td> @php
                                                                  $img = "storage/";
                                                                  if($item->fotoSealNo3)
                                                                  {
                                                                    $segel3 = $img . $item->fotoSealNo3;
                                                                  } else {
                                                                    $segel3 = 'storage/uploads/noimage.jpg';
                                                                  }
                                                                  
                                                                  @endphp
                                                              <img class="rounded mx-auto d-block" style="width: 20%" src="{{$segel3}}"   >
                                                            </td>
                                                            @else
                                                                    
                                                            @endif

                                                            @if($item->fotoSealNo4)
                                                            <td> @php
                                                                  $img = "storage/";
                                                                  if($item->fotoSealNo4)
                                                                  {
                                                                    $segel4 = $img . $item->fotoSealNo4;
                                                                  } else {
                                                                    $segel4 = 'storage/uploads/noimage.jpg';
                                                                  }
                                                                  
                                                                  @endphp
                                                              <img class="rounded mx-auto d-block" style="width: 20%" src="{{$segel4}}"   >
                                                            </td>
                                                            @else
                                                            @endif

                                                            @if($item->fotoSealNo5)
                                                            <td> @php
                                                                  $img = "storage/";
                                                                  if($item->fotoSealNo5)
                                                                  {
                                                                    $segel5 = $img . $item->fotoSealNo5;
                                                                  } else {
                                                                    $segel5 = 'storage/uploads/noimage.jpg';
                                                                  }
                                                                  
                                                                  @endphp
                                                              <img class="rounded mx-auto d-block" style="width: 20%" src="{{$segel5}}"   >
                                                            </td>
                                                            @else
                                                            @endif
                                                            @if($item->imgFormLoading)
                                                            <td> @php
                                                                  $img = "storage/";
                                                                  if($item->imgFormLoading)
                                                                  {
                                                                    $formloading = $img . $item->imgFormLoading;
                                                                  } else {
                                                                    $formloading = 'storage/uploads/noimage.jpg';
                                                                  }
                                                                  
                                                                  @endphp
                                                              <img class="rounded mx-auto d-block" style="width: 20%" src="{{$formloading}}"   >
                                                            </td>
                                                            @else
                                                            @endif
                                                          </tr>
                                                        </table>

                                                        <br>


                                                        <table>
                                                          <tr>
                                                            <td class="text-center">Bukti Review Karung 1</td>
                                                            <td class="text-center">Bukti Review Karung 2</td>
                                                            <td class="text-center">Bukti Review Karung 3</td>
                                                          </tr>
                                                          <tr>
                                                            <td class="text-center"> @php
                                                                  $img = "storage/";
                                                                  $bukti1 = $img . $item->buktiAppKarung1;
                                                                  @endphp
                                                              <img class="rounded mx-auto d-block" style="width: 20%" src="{{$bukti1}}"   >
                                                            </td>
                                                            <td class="text-center"> @php
                                                                    $img = "storage/";
                                                                    $bukti2 = $img . $item->buktiAppKarung2;
                                                                    @endphp
                                                                <img class="rounded mx-auto d-block" style="width: 20%" src="{{$bukti2}}"   >
                                                            </td>
                                                            <td class="text-center"> @php
                                                                    $img = "storage/";
                                                                    $bukti3 = $img . $item->buktiAppKarung3;
                                                                    @endphp
                                                                <img class="rounded mx-auto d-block" style="width: 20%" src="{{$bukti3}}"   >
                                                            </td>
                                                          </tr>
                                                        </table>
                                                        
                                                     <table class="mt-3 text-center">
                                                     
                                                     
                                                    
                                                        <tr  class="" >
                                                                  <td style="width:32%" ><h4><b>
                                                                      Dibuat
                                                                      <br><br><br><br>
                                                                        Operator Timbangan </h4>
                                                                  </td>
                                                                   
                                                                    <td style="width:20%"  ><h4><b>
                                                                      Mengetahui 
                                                                      <br><br><br><br>
                                                                      SPV Jembatan Timbang</h4>
                                                                    </td>
                                                                    
                                                                    
                                                        </tr>
                                                        
                                                           
                                                    
                                                     </table>
                                                       
                                                        
                                                        </div>
                                                    </div>
         
    @endforeach
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
