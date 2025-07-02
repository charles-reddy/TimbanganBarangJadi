<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Avgkarungabnormal extends Component
{
    use WithPagination;
    public $katakunci;
    public $tglout;
    
    public function render()
    {

        $dataabnormal = DB::connection('sqlsrv')->table('trscale')->join('createspms', 'createspms.id', 'trscale.spmID')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'trscale.itemCode')->join('customers', 'customers.custID', 'trscale.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->whereNotNull('netto')->where('trscale.isApp','=', 1)->select('createspms.id as spmID','createspms.sealNo1','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type', 'trscale.id as trsID', 'trscale.jam_out', 'trscale.timbangin', 'trscale.timbangout', 'trscale.netto', 'trscale.avgkarung', 'createspms.sealNo', 'createsppbs.sppbNo', 'create_t_m_s.pendfNo' )->paginate(10);
        // dd($dataabnormal);
        return view('livewire.avgkarungabnormal',['dataabnormal' => $dataabnormal]);
    }
}
