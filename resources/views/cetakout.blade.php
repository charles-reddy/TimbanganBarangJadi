<x-app-layout>
   
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
@foreach ($strukout as $item)
    

    <div class="py-12">
       {{-- tempate struk --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="container">
                        <div class="row gutters">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="card">
                                        <div class="card-body p-0">
                                            <div class="invoice-container">
                                                <div class="invoice-header">
                                                    <!-- Row start -->
                                                    <div class="row gutters">
                                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                                            <div class="custom-actions-btns mb-5">
                                                                <a href="#" class="btn btn-primary">
                                                                    <i class="icon-download"></i> Download
                                                                </a>
                                                                <a href="#" class="btn btn-secondary">
                                                                    <i class="icon-printer"></i> Print
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Row end -->
                                                    <!-- Row start -->
                                                    <div class="row gutters">
                                                        
                                                        <div class="col-lg-12 col-md-6 col-sm-6 text-center">
                                                            <address class="text-right">
                                                                   <h2 > BUKTI TIMBANG</h2>
                                                               <br> PT Kebun Tebu Mas
                                                               <br> Jl Raya Babat Jombang Km 25.5
                                                               <br> Ds. Lamongrejo Kec. Ngimbang - Lamongan
                                                            </address>
                                                        </div>
                                                    </div>
                                                    <!-- Row end -->
                                                    <!-- Row start -->
                                                    <div class="row gutters">
                                                        <div class="col-xl-8 col-lg-9 col-md-12 col-sm-12 col-12">
                                                            <div class="invoice-details">
                                                                <address class="ml-2">
                                                                    Customer &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;         :  &nbsp;&nbsp; {{ $item->custName }}
                                                                  <br> No Kendaraan  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  : &nbsp;&nbsp;  {{ $item->carID }}
                                                                  <br> Nama Transporter : &nbsp;&nbsp; {{ $item->transpName }}
                                                                </address>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-3 col-lg-3 col-md-12 col-sm-8 col-8">
                                                            <div class="invoice-details">
                                                                <div class="invoice-num">
                                                                    
                                                                    <div>
                                                                     Timbang Masuk :  {{ date('Y-m-d h:i',strtotime($item->jam_in)) }}
                                                                    <br> Timbang Keluar : {{ date('Y-m-d h:i',strtotime($item->jam_in)) }}
                                                                    </div>
                                                                </div>
                                                            </div>													
                                                        </div>
                                                    </div>
                                                    <!-- Row end -->
                                                </div>
                                                <div class="invoice-body">
                                                    <!-- Row start -->
                                                    <div class="row gutters">
                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                            <div class="table-responsive">
                                                                <table class="table custom-table m-0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Items</th>
                                                                            <th>Product ID</th>
                                                                            <th>Gross</th>
                                                                            <th>Car</th>
                                                                            <th>Netto</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                {{ $item->itemName }}
                                                                                <p class="m-0 text-muted">
                                                                                   
                                                                                </p>
                                                                            </td>
                                                                            <td>{{ $item->itemCode }}</td>
                                                                            <td>{{ $item->timbangin }} {{ $item->uom }}</td>
                                                                            <td>{{ $item->timbangout }} {{ $item->uom }}</td>
                                                                            <td>{{ $item->netto }} {{ $item->uom }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                
                                                                                <p class="m-0 text-muted">
                                                                                    
                                                                                </p>
                                                                            </td>
                                                                            <td></td>
                                                                            <td></td>
                                                                            <td></td>
                                                                        </tr>
                                                                       
                                                                        <tr>
                                                                            <td>&nbsp;</td>
                                                                            <td colspan="2">
                                                                                <p>
                                                                                    Dibuat<br>
                                                                                      <br>
                                                                                    <br>
                                                                                </p>
                                                                                <h5 class="text-success"><strong>{{ Auth::user()->name }}</strong></h5>
                                                                            </td>			
                                                                            <td>
                                                                                <p>
                                                                                   Driver<br>
                                                                                    <br>
                                                                                    <br>
                                                                                </p>
                                                                                <h5 class="text-success"><strong>{{ $item->driver }}</strong></h5>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Row end -->
                                                </div>
                                                <div class="invoice-footer">
                                                    Thank you for your Business.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        {{-- tempate struk --}}
        
    </div>
    @endforeach
    
</x-app-layout>
