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
    
@foreach ($strukspm as $item)
    

    
                                                    <div class="container">
                                                        <a class="btn-primary" href='/createspm' id="print">Back</a>
                                                        <div class="row">
                                                            <div class="col-sm ">
                                                            <h4>   <b>PT Kebun Tebu Mas &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                              Nomor : {{$item->spmNo}} </b>  </h4> 
                                                            <h4>   <b>  Jl Raya Babat Jombang Km 25.5 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                              Tgl : {{ date('d-m-Y ',strtotime($item->tglSpm)) }} </b> </h4>  
                                                            <h4>   <b> Ds. Lamongrejo - Kec. Ngimbang</b>  </h4>                                               
                                                            </div>
                                                            
                                                        </div> 
                                                      
                                                        
                                                         <div class="row">
                                                            <div class="col-sm-5">
                                                              <h4 align="center"><b>SURAT PERINTAH MUAT</b></h4>
                                                            <h4>   <b>    nopol :   {{$item->carID}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Nomor : {{$item->sppbNo}} </b></h4>
                                                                                                 
                                                            </div>
                                                            
                                                           
                                                        </div> 
                                                            
                                                        <table  >
                                                            <thead >
                                                                    @php
                                                                          $no = 1;
                                                                        @endphp
                                                                        
                                                            <tr>
                                                                <td class="border border-dark border-5"><h4><b> No. </h4></td>
                                                                <td class="border border-dark border-5" ><h4><b>Nama Barang</h4></td>
                                                                <td class="border border-dark border-5"><h4><b>Jumlah Karung</h4></td>
                                                                <td  class="border border-dark border-5" ><h4><b>Keterangan</h4></td>
                                                            </tr>
                                                            </thead>
                                                            <tr>
                                                                <td class="border border-dark border-5"><h4><b>@php echo $no;  @endphp </h4></td>
                                                                <td class="border border-dark border-5" ><h4><b>{{$item->itemName}} </h4>
                                                                <h4><b>{{$item->custName}} </h4>
                                                                <h4><b> {{$item->remarks}}</h4>
                                                                </td>
                                                                <td class="border border-dark border-5"><h4><b> @php 
                                                                    $karung = number_format($item->qtyKarung);
                                                                    $kg = number_format($item->qtyKg);
                                                                    
                                                                    if ($karung) {
                                                                      echo "Berat = $kg KG <br>" ; 
                                                                      echo "Karung = $karung zak"; 
                                                                    } else {
                                                                      echo "Berat = $kg KG ";  
                                                                    } @endphp</h4>
                                                    
                                                                </td>
                                                    
                                                                <td class="border border-dark border-5"><h4><b>
                                                                    Kondisi Kendaraan <br>
                                                                    
                                                                          <input type="checkbox" >  Bersih&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                          <input type="checkbox" >  Kering&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp; 
                                                                          <input type="checkbox" >  Alas&nbsp;&nbsp;&nbsp;     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  
                                                                   
                                                                          <br>Kondisi Kemasan / Karung <br>
                                                                          <input type="checkbox" >  Bersih&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                                                          <input type="checkbox" >  Kering&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;
                                                                          <input type="checkbox" >  Utuh&nbsp;&nbsp;&nbsp;   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                          </h4>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                  <td class="border border-dark border-5"></td>
                                                                  <td class="border border-dark border-5" ><h4><b>
                                                                  Jumlah </h4>
                                                                  </td>
                                                                    <td class="border border-dark border-5"><h4><b>
                                                                    @php echo number_format($item->qtyKarung) @endphp </h4>
                                                                    
                                                                    </td>
                                                                    <td class="border border-dark border-5"></td>
                                                                    
                                                                    
                                                            </tr>
                                                            <tr>
                                                                  <td colspan="5" class="border border-dark border-5">
                                                                  <h4><b>Terbilang : {{$item->terbilangkarung}}</h4>
                                                                  </td>
                                                                    
                                                                    
                                                               </tr>
                                                        </table>
                                                     <table>
                                                     
                                                     
                                                    
                                                        <tr   >
                                                                  <td  class="border border-dark border-5" ><h4><b>
                                                                      disetujui oleh
                                                                      <br><br><br><br>
                                                                        Kepala Admin Gudang </h4>
                                                                  </td>
                                                                    <td class="border border-dark border-5"><h4><b>
                                                                      disaksikan oleh
                                                                      <br><br><br><br>
                                                                      &nbsp;Pemeriksa / security&nbsp;</h4>
                                                                    </td>
                                                                    <td class="border border-dark border-5"><h4><b>
                                                                      dibuat oleh
                                                                      <br><br><br><br>
                                                                      &nbsp;Admin Gudang&nbsp;</h4>
                                                                    </td>
                                                                    <td class="border border-dark border-5"><h4><b>
                                                                      diserahkan oleh&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                      <br><br><br>
                                                                      Petugas Gudang</h4>
                                                                    </td>
                                                                    
                                                               </tr>
                                                                <tr>
                                                                  <td colspan="8" class="border border-dark border-5"><h4><b>
                                                                  Putih Gudang   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   Merah Admin Gudang  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Biru Laboratorium <br>Seal No = {{$item->sealNo}}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Kontainer No = {{$item->kontainerNo}} 
                                                                  </td>
                                                                  </h4>
                                                        </tr>
                                                           
                                                    
                                                     </table>
                                                       
                                                        
                                                        </div>
                                                    </div>
         
    @endforeach
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
