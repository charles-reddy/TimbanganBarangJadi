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
            // $registered = DB::connection('sqlsrv')->table('createspms')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('products', 'products.itemCode', 'createspms.itemCode')->join('customers', 'customers.custID', 'createspms.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->whereDate('tglSpm','=', Carbon::now())->where('isIN','=',0)->where('createspms.carID','like','%' . $this->katakunci . '%')->select('createspms.id as spmID','createspms.spmNo','createspms.carID','createspms.driver','createspms.qtyKg','create_t_m_s.tglMuat','create_t_m_s.pendfNo','products.itemName','customers.custName','jenistruks.jenisTruk')->paginate(10);

            $registered = DB::connection('sqlsrv')->table('create_t_m_s')->join('createspms','createspms.tiketID','create_t_m_s.id')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('jenistruks', 'jenistruks.id', 'create_t_m_s.jenisTruk')->whereDate('create_t_m_s.tglMuat','=', Carbon::now())->whereNotNull('isSPM')->where('createspms.carID','like','%' . $this->katakunci . '%')->select('createspms.id as spmID','createspms.spmNo','createspms.carID','createspms.driver','createspms.qtyKg','create_t_m_s.tglMuat','create_t_m_s.pendfNo','products.itemName','customers.custName','jenistruks.jenisTruk')->paginate(10);
        
        } else {
            // $registered = DB::connection('sqlsrv')->table('createspms')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('products', 'products.itemCode', 'createspms.itemCode')->join('customers', 'customers.custID', 'createspms.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->whereDate('tglSpm','=', Carbon::now())->where('isIN','=',0)->select('createspms.id as spmID','createspms.spmNo','createspms.carID','createspms.driver','createspms.qtyKg','create_t_m_s.tglMuat','create_t_m_s.pendfNo','products.itemName','customers.custName','jenistruks.jenisTruk')->paginate(10);
            $registered = DB::connection('sqlsrv')->table('create_t_m_s')->join('createspms','createspms.tiketID','create_t_m_s.id')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('jenistruks', 'jenistruks.id', 'create_t_m_s.jenisTruk')->whereDate('create_t_m_s.tglMuat','=', Carbon::now())->whereNotNull('isSPM')->select('createspms.id as spmID','createspms.spmNo','createspms.carID','createspms.driver','createspms.qtyKg','create_t_m_s.tglMuat','create_t_m_s.pendfNo','products.itemName','customers.custName','jenistruks.jenisTruk')->paginate(10);
            // dd(($data));
        }
        // dd($registered);
        return view('livewire.cardregistered', ['registered' => $registered]);
    }
}
