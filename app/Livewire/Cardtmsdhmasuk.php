<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Cardtmsdhmasuk extends Component
{
    public function render()
    {
        $tmsdhdatang = DB::connection('sqlsrv')->table('create_t_m_s')->leftJoin('customers', 'customers.custID', 'create_t_m_s.custID')->whereDate('tglMuat','=', date('Y-m-d',strtotime(Carbon::now())) )->wherenotnull('isSecCek')->orderBy('isSecCekDate')->paginate(50);

         $registered = DB::connection('sqlsrv')->table('createspms')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('products', 'products.itemCode', 'createspms.itemCode')->join('customers', 'customers.custID', 'createspms.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->whereDate('tglSpm','=', Carbon::now())->select('createspms.id as spmID','createspms.spmNo','createspms.carID','createspms.driver','createspms.qtyKg','create_t_m_s.tglMuat','create_t_m_s.pendfNo','products.itemName','customers.custName','jenistruks.jenisTruk','isSecCekDate')->orderBy('isSecCekDate')->paginate(50);
       

        // dd($tmsdhdatang, $registered);
        
        return view('livewire.cardtmsdhmasuk', ['registered' => $registered, 'tmsdhdatang' => $tmsdhdatang]);
    }
}
