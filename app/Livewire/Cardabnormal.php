<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Cardabnormal extends Component
{
    use WithPagination;
    public $katakunci;
    public $tglout;
    
    public function render()
    {
        $tglout = DB::connection('sqlsrv')->table('trscale')->where('isApp',1)->whereNotNull('netto')->orderBy('id','desc')->first();
        if ($this->katakunci !=null) {

            $dataabnormal = DB::connection('sqlsrv')->table('trscale')->join('createspms', 'createspms.id', 'trscale.spmID')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'trscale.itemCode')->join('customers', 'customers.custID', 'trscale.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->where('isApp',1)->wherenotNull('timbangout')->where('trscale.carID','like','%' . $this->katakunci . '%')->select('createspms.id as spmID','createspms.sealNo1','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type', 'trscale.id as trsID', 'trscale.jam_out', 'trscale.timbangin', 'trscale.timbangout', 'trscale.netto', 'trscale.avgkarung', 'createspms.sealNo', 'createsppbs.sppbNo', 'create_t_m_s.pendfNo' )->paginate(10);
        } elseif (($this->tglout  ) !=null) {
            $dataabnormal = DB::connection('sqlsrv')->table('trscale')->join('createspms', 'createspms.id', 'trscale.spmID')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'trscale.itemCode')->join('customers', 'customers.custID', 'trscale.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->where('isApp',1)->wheredate('jam_out','=',$this->tglout)->wherenotNull('timbangout')->select('createspms.id as spmID','createspms.sealNo1','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type', 'trscale.id as trsID', 'trscale.jam_out', 'trscale.timbangin', 'trscale.timbangout', 'trscale.netto', 'trscale.avgkarung', 'createspms.sealNo', 'createsppbs.sppbNo', 'create_t_m_s.pendfNo' )->paginate(10);
       
        } else {
            $this->tglout = $tglout->jam_out;
            $dataabnormal = DB::connection('sqlsrv')->table('trscale')->join('createspms', 'createspms.id', 'trscale.spmID')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'trscale.itemCode')->join('customers', 'customers.custID', 'trscale.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->where('isApp',1)->wheredate('jam_out','=',$this->tglout)->wherenotNull('timbangout')->select('createspms.id as spmID','createspms.sealNo1','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type', 'trscale.id as trsID', 'trscale.jam_out', 'trscale.timbangin', 'trscale.timbangout', 'trscale.netto', 'trscale.avgkarung', 'createspms.sealNo', 'createsppbs.sppbNo', 'create_t_m_s.pendfNo' )->paginate(10);
            
        }
        // dd($dataabnormal);
        return view('livewire.cardabnormal',['dataabnormal' => $dataabnormal]);
    }
}
