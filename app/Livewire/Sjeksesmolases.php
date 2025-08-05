<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Sjeksesmolases extends Component

{
    use WithPagination;

    public function render()
    {
        $data = DB::connection('sqlsrv')->table('createspms')->join('customers', 'customers.custID', 'createspms.custID')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'createspms.itemCode')->where('eksesMol','=', 1)->select('createspms.id','createspms.spmNo','createspms.tiketID','createspms.tglSpm','createspms.custID','createspms.qtyKarung','createspms.qtyKg','createspms.packingID','createspms.sppbNo','createspms.carID','createspms.driver','createspms.dnNo','createspms.sealNo','createspms.kontainerNo','createspms.spmNo','customers.custName','customers.custAdd','createsppbs.tglSppb','createsppbs.kontrakNo','createsppbs.poNo','products.itemName','products.uom')->paginate(10);
        // dd($data);
        return view('livewire.sjeksesmolases', ['dataeksesmol' => $data]);
    }
}
