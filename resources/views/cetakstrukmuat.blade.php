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
  
                   
                        
                
       
    @foreach ($struktiketmuat as $item)
    <table>
        <tr>
            <td> </td>
        </tr>
    </table>
    
        <div class="container mt-2">
            <a class="btn-primary" href='/timkeluar' id="print">Back</a>
            <div class="row text-center">
                 PT KEBUN TEBU MAS  <br>
                {!! DNS1D::getBarcodesvg("$item->id", 'C39E+',3,100) !!}
                <br>

                 {{ $item->pendfNo }}
                                                           
            </div>
           
            <br>
            </div>
            <table >
                
            
                <tr>
                    <td>
                       <b> Tgl Daftar  : </b>  {{ date('d-m-Y H:i',strtotime($item->tglDaftar)) }} 
                      
                      
                    </td>
                </tr>
                <tr>
                    <td>
                       <b> Tgl Muat  : </b>  {{ date('d-m-Y',strtotime($item->tglMuat)) }} 
                      
                      
                    </td>
                </tr>
                <tr>    
                    <td>
                        <b>No SPPB : </b> {{ $item->sppbNo }}
                    </td>
                 </tr>   
                 <tr> 
                 <tr>    
                    <td>
                        <b>Customer : </b> {{ $item->custName }}
                    </td>
                 </tr>   
                 <tr>    
                 <tr>    
                    <td>
                        <b>Barang : </b> {{ $item->itemName}}
                    </td>
                 </tr>   
                 <tr> 
                    <td>
                        <b>Jml Karung : </b> {{ $item->tmQtyKarung }}
                    </td>
                 </tr> 
                 <tr>    
                    <td>
                        <b>Berat : </b> {{ $item->tmQtyKg }}
                    </td>
                 </tr> 
                 <tr>    
                    <td>
                        <b>Nama Transporter : </b> {{ $item->tmTranspName }}
                    </td>
                 </tr> 
                 <tr>    
                    <td>
                        <b>Driver : </b> {{ $item->tmDriver }}
                    </td>
                 </tr> 
                 <tr>    
                    <td>
                        <b>Nopol : </b> {{ $item->tmCarID }}
                    </td>
                 </tr> 
                 <tr>    
                    <td>
                        <b>No HP Driver : </b> {{ $item->noHpDriver }}
                    </td>
                 </tr> 
                 <tr>    
                    <td>
                        <b>Jenis Truk : </b> {{ $item->jenisTruk }}
                    </td>
                 </tr> 
                
                 
                    foto
            </table>
            <br><br>
            {{ $tglnow }}  

            

            
        </div>
        

        
        

    @endforeach

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>