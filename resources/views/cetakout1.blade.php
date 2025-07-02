<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk Timbang</title>
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
    
       
    @foreach ($strukout as $item)
        <div class="container mt-2">
            <a class="btn-primary" href='/timkeluar' id="print">Back</a>
            <div class="row text-center">
                <h3 > BUKTI TIMBANG</h3>
                                                                 <h4>PT Kebun Tebu Mas</h4>
                                                                 <h4>Jl Raya Babat Jombang Km 25.5</h4>
                                                                <h4>Ds. Lamongrejo Kec. Ngimbang - Lamongan</h4>
                                                                
            </div>
            <hr>
            <br>
            </div>
            <table >
                <tr>
                    <td>
                        <h4>SO  : {{ $item->sppbNo }}</h4><br>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h4>DN  : {{ $item->dnNo }}</h4><br>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h4>No Sequence  : {{ $item->id }}</h4><br>
                    </td>
                    
                    <td>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                    <td >
                        <h4 >Nama Produk : {{ $item->itemName }}</h4>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h4 >Tgl Masuk : {{ date('d-m-Y H:i:s',strtotime($item->jam_in)) }}</h4><br> 
                        
                    </td>
                    <td>
                        &nbsp;
                    </td>
                    <td >
                        <h4>Gross : {{ number_format($item->timbangout) }}</h4>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h4 >Tgl Keluar : {{ date('d-m-Y H:i:s',strtotime($item->jam_out)) }}</h4>
                        
                    </td>
                    <td>
                        &nbsp;
                    </td>
                    <td >
                        <h4>Berat Kendaraan : {{ number_format($item->timbangin) }}</h4>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h4>No Kendaraan : {{ $item->carID }}</h4>
                        
                    </td>
                    <td>
                        &nbsp;
                    </td>
                    <td >
                        <h4>Netto : {{ number_format($item->netto) }}</h4>
                    </td>
                </tr>
                <tr>
                    <td>
                         <h4 >Nama Customer : {{ $item->custName }}</h4>
                    </td>
                    <td>
                        &nbsp;
                    </td>
                    <td>
                        <h4 >Jumlah Karung : {{ $item->b10QtyKarung }}</h4>
                    </td>
                </tr>
                <tr>
                    <td>
                         <h4 >Remarks : {{ $item->remarks }}</h4>
                    </td>
                    <td>
                        &nbsp;
                    </td>
                    <!-- <td><h4>
                        @php
                            if(str_contains($item->itemName, '50Kg'))
                            {
                                echo 'Rata-rata Karung : ' . number_format($item->avgKarung,2);
                            }

                        @endphp
                        
                        </h4>
                        
                    </td> -->
                </tr>
                
            </table>
            
            

            <hr>
            <br>
            <table>
                <tr>
                    <td>Dibuat</td>
                    <td>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                    <td>Driver</td>
                    <td>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                    <td>Diperiksa</td>
                </tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td>&nbsp;</td></tr>
                <tr>
                    <td>{{ Auth::user()->name }}</td>
                    <td>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                    <td>{{ $item->driver }}</td>
                    <td>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                    <td>____________</td>
                </tr>
            </table>

            
        </div>
        

        
        

    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>