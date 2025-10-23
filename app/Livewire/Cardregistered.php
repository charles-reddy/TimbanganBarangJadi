<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Cardregistered extends Component
{
    use WithPagination;
    public $katakunci;
    public function render()
    {
        

        if ($this->katakunci !=null) {
            $registered = DB::connection('sqlsrv')->table('createspms')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('products', 'products.itemCode', 'createspms.itemCode')->join('customers', 'customers.custID', 'createspms.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->whereDate('tglSpm','=', Carbon::now())->where('isIN','=',0)->where('createspms.carID','like','%' . $this->katakunci . '%')->select('createspms.id as spmID','createspms.spmNo','createspms.carID','createspms.driver','createspms.qtyKg','create_t_m_s.tglMuat','create_t_m_s.pendfNo','products.itemName','customers.custName','jenistruks.jenisTruk')->paginate(10);
        // dd($registered);
        } else {
            $registered = DB::connection('sqlsrv')->table('createspms')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('products', 'products.itemCode', 'createspms.itemCode')->join('customers', 'customers.custID', 'createspms.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->whereDate('tglSpm','=', Carbon::now())->where('isIN','=',0)->select('createspms.id as spmID','createspms.spmNo','createspms.carID','createspms.driver','createspms.qtyKg','create_t_m_s.tglMuat','create_t_m_s.pendfNo','products.itemName','customers.custName','jenistruks.jenisTruk')->paginate(10);
       
        }

        return view('livewire.cardregistered', ['registered' => $registered]);
    }
}
