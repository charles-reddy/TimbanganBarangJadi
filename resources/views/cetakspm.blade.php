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
                                                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                              Nomor : {{$item->spmNo}} </b>  </h4> 
                                                            <h4>   <b>  Jl Raya Babat Jombang Km 25.5 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                              Tgl : {{ date('d-m-Y ',strtotime($item->tglSpm)) }} </b> </h4>  
                                                            <h4>   <b> Ds. Lamongrejo - Kec. Ngimbang</b>  </h4>                                               
                                                            </div>
                                                            
                                                        </div> 
                                                      
                                                        
                                                         <div class="row">
                                                            <div class="col-sm-5">
                                                              <h4 align="center"><b>SURAT PERINTAH MUAT</b></h4>
                                                            <h4>   <b>    nopol :   {{$item->carID}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Nomor : {{$item->sppbNo}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;      No DN : {{$item->dnNo}} </b></h4>
                                                                                                 
                                                            </div>
                                                            
                                                           
                                                        </div> 
                                                            
                                                        <table  >
                                                            <thead >
                                                                    @php
                                                                          $no = 1;
                                                                        @endphp
                                                                        
                                                            <tr>
                                                                <td class="col-sm-1" style="width:10%"><h4><b> No. </h4></td>
                                                                <td class="col-md-1" colspan="2"  style="width:25%"><h4><b>Nama Barang</h4></td>
                                                                <td class="col-md-1" style="width:20%"><h4><b>Jumlah Karung</h4></td>
                                                                <td class="col-md-1"><h4><b>Susunan Muatan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h4></td>
                                                                <td class="col-md-1"  ><h4><b>Kondisi Bak/Box  </h4></td>
                                                            </tr>
                                                            </thead>
                                                            <tr>
                                                                <td ><h4><b>@php echo $no;  @endphp </h4></td>
                                                                <td colspan="2"  ><h4><b>{{$item->itemName}} </h4>
                                                                <h4><b>{{$item->custName}} </h4>
                                                                <h4><b> {{$item->remarks}}</h4>
                                                                </td>
                                                                <td ><h4><b> @php 
                                                                    $karung = number_format($item->qtyKarung);
                                                                    $kg = number_format($item->qtyKg);
                                                                    
                                                                    if ($karung) {
                                                                      echo "Berat = $kg KG <br>" ; 
                                                                      echo "Karung = $karung zak"; 
                                                                    } else {
                                                                      echo "Berat = $kg KG ";  
                                                                    } @endphp</h4>
                                                    
                                                                </td>
                                                                <td>
                                                                  1. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 6. &nbsp;&nbsp;&nbsp; <br>
                                                                  2. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 7. &nbsp;&nbsp;&nbsp;<br>
                                                                  3. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 8. &nbsp;&nbsp;&nbsp;<br>
                                                                  4. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 9. &nbsp;&nbsp;&nbsp;<br>
                                                                  5. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 10. &nbsp;&nbsp;&nbsp;<br>
                                                                </td>
                                                    
                                                                <td ><h4><b>
                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                  <td ></td>
                                                                  <td colspan="2"  ><h4><b>
                                                                  Jumlah </h4>
                                                                  </td>
                                                                    <td ><h4><b>
                                                                    @php echo number_format($item->qtyKarung) @endphp </h4>
                                                                    
                                                                    </td>
                                                                    <td   >Tanggal Produksi:   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  </td>
                                                                    <td   >Lot No:</td>
                                                                    
                                                                    
                                                            </tr>
                                                            <tr>
                                                                  <td colspan="3" >
                                                                  <h4><b>Terbilang : {{$item->terbilangkarung}} </h4>
                                                                  </td>
                                                                  <td colspan="3" >
                                                                  <h4><b> Kondisi Produk: &nbsp;&nbsp;&nbsp;&nbsp; Bersih  &nbsp;&nbsp;&nbsp; Kering &nbsp;&nbsp;&nbsp;  Utuh &nbsp;&nbsp;&nbsp;</h4>
                                                                  
                                                                  </td>
                                                                    
                                                                    
                                                               </tr>
                                                        </table>
                                                     <table>
                                                     
                                                     
                                                    
                                                        <tr   >
                                                                  <td style="width:32%" ><h4><b>
                                                                      disetujui oleh
                                                                      <br><br><br><br>
                                                                        Kepala Admin Gudang </h4>
                                                                  </td>
                                                                    <td style="width:18%"  ><h4><b>
                                                                      disaksikan oleh
                                                                      <br><br><br><br>
                                                                      Pemeriksa/security</h4>
                                                                    </td>
                                                                    <td style="width:20%"  ><h4><b>
                                                                      dibuat oleh
                                                                      <br><br><br><br>
                                                                      &nbsp;Admin Gudang&nbsp;</h4>
                                                                    </td>
                                                                    <td style="width:20%"  ><h4><b>
                                                                      diserahkan oleh  
                                                                      <br><br><br><br>
                                                                      Petugas Gudang</h4>
                                                                    </td>
                                                                    
                                                                    
                                                        </tr>
                                                        <tr>
                                                                  <td colspan="4" ><h4><b>
                                                                  Putih Gudang   &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; Merah Admin Gudang  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Biru Laboratorium <br>Seal No = {{$item->sealNo}}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Kontainer No = {{$item->kontainerNo}} 
                                                                  </h4></td>
                                                                  
                                                        </tr>
                                                           
                                                    
                                                     </table>
                                                       
                                                        
                                                        </div>
                                                    </div>
         
    @endforeach
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
