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

  <body onload="window.print()">
    
@foreach ($strukspm as $item)
    

    
                                                    <div class="container">
                                                        <a class="btn-primary" href='/createspm' id="print">Back</a>
                                                        <div class="row">
                                                            <div class="col-sm-9">
                                                            <h5>   <b>PT Kebun Tebu Mas</b>  </h5> 
                                                            <h5>   <b>  Jl Raya Babat Jombang Km 25.5</b> </h5>  
                                                            <h5>   <b> Ds. Lamongrejo - Kec. Ngimbang</b>  </h5>                                               
                                                            </div>
                                                            <div class="col-sm" >
                                                            <h5>   <b>  Nomor : {{$item->spmNo}} </b></h5> 
                                                            <h5>   <b>   Tgl : {{ date('d-m-Y H:i',strtotime($item->tglSpm)) }} </b>  </h5>                                                       
                                                            </div>
                                                        </div> 
                                                      
                                                        <div class="form-group"><br>
                                                         <h3> <p align="center"><b>SURAT PERINTAH MUAT</p> </h3>
                                                         <div class="row">
                                                            <div class="col-sm-5">
                                                            <h5>   <b>    nopol :   {{$item->carID}} </b></h5>
                                                                                                 
                                                            </div>
                                                            <div class="col" >
                                                            <h5>   <b>    Nomor : {{$item->sppbNo}}<br></b> </h5>
                                                                                                                        
                                                            </div>
                                                           
                                                        </div> 
                                                            
                                                        <table border="1" style="width: 100%">
                                                            <thead >
                                                                    @php
                                                                          $no = 1;
                                                                        @endphp
                                                                        
                                                            <tr>
                                                                <th class="border border-dark border-5"><h5><b> No. </h5></th>
                                                                <th class="border border-dark border-5"><h5><b>Nama Barang</h5></th>
                                                                <th class="border border-dark border-5"><h5><b>Jumlah Karung</h5></th>
                                                                <th class="border border-dark border-5"><h5><b>Keterangan</h5></th>
                                                            </tr>
                                                            </thead>
                                                            <tr>
                                                                <th class="border border-dark border-5"><h5><b>@php echo $no;  @endphp </h5></th>
                                                                <th class="border border-dark border-5"><h5><b>{{$item->itemName}} </h5><br>
                                                                <h5><b>{{$item->transpName}} <br></h5>
                                                                <h5><b> {{$item->remarks}}</h5>
                                                                </th>
                                                                <th class="border border-dark border-5"><h5><b> @php 
                                                                    $karung = number_format($item->qtyKarung);
                                                                    $kg = number_format($item->qtyKg);
                                                                    
                                                                    if ($karung) {
                                                                      echo "Berat = $kg KG <br>";  
                                                                      echo "Karung = $karung Sak"; 
                                                                    } else {
                                                                      echo "Berat = $kg KG ";  
                                                                    } @endphp</h5>
                                                    
                                                                </th>
                                                    
                                                                <th class="border border-dark border-5"><h5><b>
                                                                    Kondisi Kendaraan <br>
                                                                    
                                                                          <input type="checkbox" >  Bersih&nbsp;&nbsp;&nbsp;    
                                                                          <input type="checkbox" >  Kering&nbsp;&nbsp;&nbsp;   
                                                                          <input type="checkbox" >  Alas&nbsp;&nbsp;&nbsp;      
                                                                   
                                                                    <br> Kondisi Kemasan / Karung <br>
                                                                          <input type="checkbox" >  Bersih&nbsp;&nbsp;&nbsp;   
                                                                          <input type="checkbox" >  Kering&nbsp;&nbsp;&nbsp;  
                                                                          <input type="checkbox" >  Utuh&nbsp;&nbsp;&nbsp;   
                                                                          </h5>
                                                                </th>
                                                                </tr>
                                                                <tr>
                                                                  <th class="border border-dark border-5"></th>
                                                                  <th class="border border-dark border-5"><h5><b>
                                                                  Jumlah </h5>
                                                                  </th>
                                                                    <th class="border border-dark border-5"><h5><b>
                                                                    @php echo number_format($item->qtyKarung) @endphp </h5>
                                                                    
                                                                    </th>
                                                                    <th class="border border-dark border-5"></th>
                                                                    
                                                                    
                                                               </tr>
                                                               <tr>
                                                                  <th colspan="4" class="border border-dark border-5">
                                                                  <h5><b>Terbilang : {{$item->terbilangkarung}}</h5>
                                                                  </th>
                                                                    
                                                                    
                                                               </tr>
                                                      </table>
                                                     <table border="1" style="width: 100%"  >
                                                     
                                                     
                                                    
                                                     <tr  height= "180px" >
                                                                  <th  class="border border-dark border-5" ><h5><b>
                                                                      disetujui oleh
                                                                      <br><br><br><br><br>
                                                                        Kepala Admin Gudang </h5>
                                                                  </th>
                                                                    <th class="border border-dark border-5"><h5><b>
                                                                      disaksikan oleh
                                                                      <br><br><br><br><br>
                                                                      Pemeriksa / security</h5>
                                                                    </th>
                                                                    <th class="border border-dark border-5"><h5><b>
                                                                      dibuat oleh
                                                                      <br><br><br><br><br>
                                                                      Admin Gudang</h5>
                                                                    </th>
                                                                    <th class="border border-dark border-5"><h5><b>
                                                                      diserahkan oleh
                                                                      <br><br><br><br><br>
                                                                      Petugas Gudang</h5>
                                                                    </th>
                                                                    
                                                               </tr>
                                                                <tr>
                                                                  <th colspan="4" class="border border-dark border-5"><h5><b>
                                                                  Putih Gudang   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   Merah Admin Gudang  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Biru Laboratorium <br>Seal No = {{$item->sealNo}}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Kontainer No = {{$item->kontainerNo}} 
                                                                  </th>
                                                                  </h5>
                                                                </tr>
                                                           
                                                    
                                                     </table>
                                                       
                                                        
                                                        </div>
                                                    </div>
         
    @endforeach
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
