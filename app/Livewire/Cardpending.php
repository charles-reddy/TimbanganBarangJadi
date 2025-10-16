<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Cardpending extends Component
{
    use WithPagination;
    public function render()
    {
        
        // $registrasikmrblmmasuk = DB::connection('sqlsrv')->table('createspms')->leftJoin('create_t_m_s', 'create_t_m_s.id','createspms.tiketID')->leftJoin('customers', 'customers.custID', 'create_t_m_s.custID')->whereDate('tglSpm','=', Carbon::now()->addDays(-1) )->where('isIN','=',0)->paginate(10);
        $registrasikmrblmmasuk = DB::connection('sqlsrv')->table('createspms')->leftJoin('create_t_m_s', 'create_t_m_s.id','createspms.tiketID')->leftJoin('customers', 'customers.custID', 'create_t_m_s.custID')->whereDate('tglSpm','=', Carbon::now()->addDays(-1) )->where('isIN','=',0)->paginate(20);
        // $timbanginkmrblmkeluar = DB::connection('sqlsrv')->table('trscale')->leftJoin('createspms', 'createspms.id','trscale.spmID')->leftJoin('create_t_m_s', 'create_t_m_s.id','createspms.tiketID')->leftJoin('customers', 'customers.custID', 'create_t_m_s.custID')->whereDate('trscale.created_at','=', Carbon::now()->addDays(-1) )->wherenull('timbangout')->paginate(10);
        $timbanginkmrblmkeluar = DB::connection('sqlsrv')->table('trscale')->leftJoin('createspms', 'createspms.id','trscale.spmID')->leftJoin('create_t_m_s', 'create_t_m_s.id','createspms.tiketID')->leftJoin('customers', 'customers.custID', 'create_t_m_s.custID')->whereDate('trscale.created_at','=', Carbon::now()->addDays(-1) )->wherenull('timbangout')->paginate(20);
        $tidakdatang = DB::connection('sqlsrv')->table('create_t_m_s')->leftJoin('customers', 'customers.custID', 'create_t_m_s.custID')->whereDate('tglMuat','=', date('Y-m-d',strtotime(Carbon::now()->addDays(-1))) )->wherenull('isSecCek')->paginate(20);
        // dd($registrasikmrblmmasuk);

        return view('livewire.cardpending',['registrasikmrblmmasuk' => $registrasikmrblmmasuk, 'timbanginkmrblmkeluar' => $timbanginkmrblmkeluar,  'tidakdatang' => $tidakdatang]);
    }
}
