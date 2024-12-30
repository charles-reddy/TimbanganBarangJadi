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
            <br><br>

            

            <div class="row justify-content-around">
                <div class="col-4">
                    <h5>Tgl Masuk : {{ date('d-m-Y H:i',strtotime($item->jam_in)) }}</h5>
                </div>
                <div class="col-4">
                    <h5>Nama Customer : {{ $item->custName }}</h5>
                </div>
            </div>
            <div class="row justify-content-around">
                <div class="col-4">
                    <h5>Tgl Keluar : {{ date('d-m-Y H:i',strtotime($item->jam_out)) }}</h5>
                </div>
                <div class="col-4 ">
                    <h5>Nama Produk : {{ $item->itemName }}</h5>
                </div>
            </div>

            <div class="row justify-content-around">
                
                <div class="col-4 ">
                    <h5>No Kendaraan : {{ $item->carID }}</h5>
                </div>
                <div class="col-4">
                    <h5>Berat Kendaraan : {{ $item->timbangin }}</h5>
                </div>
            </div>

            <div class="row justify-content-around">
                
                <div class="col-4 ">
                    <h5>Gross : {{ $item->timbangout }}</h5>
                </div>
                <div class="col-4">
                    <h5>Netto : {{ $item->netto }}</h5>
                </div>
            </div>
            <hr>
            <br><br>

            <div class="row justify-content-around">
                <div class="col-4">
                    <h5>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dibuat</h5>
                </div>
                <div class="col-4">
                    <h5>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Driver</h5>
                </div>
                <div class="col-4">
                   <h5>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Diperiksa</h5>
                </div>
    
            </div>
            <br><br><br><br>
            <div class="row align-items-center">
                <div class="col">
                    <h5>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ Auth::user()->name }}</h5>
                </div>
                <div class="col">
                    <h5>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $item->driver }}</h5>
                </div>
                <div class="col">
                    <h5>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ____________</h5>
                </div>
    
            </div>
        </div>
        

        
        

    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>