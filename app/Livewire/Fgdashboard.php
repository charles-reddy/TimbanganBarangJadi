<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Fgdashboard extends Component
{
    public $transac;
    public $jmltruk;

    public function mount()
    {
        // $transac = DB::connection('sqlsrv')->table('trscale')->whereNot('jam_out')->orderBy('jam_out','desc')
        //     ->select(db::raw("sum(netto) as netto"), db::raw(date('d-m-Y',strtotime('jam_out'))), db::raw("id") )
        //     ->groupBy('jam_out')
        //     ->get();
        
        $transac = DB::connection('sqlsrv')->table('vwSummaryTruckFG')->orderBy('tgl','desc')->Limit(7)->get();
        // dd($transac);
        foreach($transac as $item) {
            $data['label'][] =  $item->tgl;
            $data['data'][] = (int) $item->totalNetto;
        }

        $this->transac =  json_encode($data);
        // dd($this->transac);

        $jmltruk = DB::connection('sqlsrv')->table('vwSummaryTruckFG')->orderBy('tgl','desc')->Limit(7)->get();
        // dd($transac);
        foreach($jmltruk as $item) {
            $data1['label'][] =  $item->tgl;
            $data1['data'][] = (int) $item->totalTruk;
        }

        $this->jmltruk =  json_encode($data1);
        // dd($this->jmltruk);
    }

    public function render()
    {
        $antrianskr = DB::connection('sqlsrv')->table('vwTiketMuat')->whereDate('tgl','=', Carbon::now() )->orderBy('tgl','desc')->select('antrian')->first();
        $antrianbsk = DB::connection('sqlsrv')->table('vwTiketMuat')->whereDate('tgl','=', Carbon::now()->addDays(+1) )->orderBy('tgl','desc')->select('antrian')->first();
        // dd($antrianskr, $antrianbsk);
        $registrasi = DB::connection('sqlsrv')->table('createspms')->whereDate('tglSpm','=', Carbon::now() )->where('isIN','=',0)->count('id');
        // dd($registrasi);
        $data = DB::connection('sqlsrv')->table('vwSummaryTruckFG')->orderBy('tgl','desc')->first();
        $data7hari = DB::connection('sqlsrv')->table('create_t_m_s')->join('customers','customers.custID','create_t_m_s.custID')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->join('products','products.itemCode','create_t_m_s.itemCode')->join('jenistruks', 'jenistruks.id', 'create_t_m_s.jenisTruk')->whereBetween('tglMuat',[Carbon::now(), Carbon::now()->addDays(+7) ])->where('create_t_m_s.tmQtyKg','>',0)->orderBy('tglMuat','asc')->paginate(10);
        // dd($data7hari);
        $dataout = DB::connection('sqlsrv')->table('trscale')->join('customers','customers.custID','trscale.custID')->join('products','products.itemCode','trscale.itemCode')->join('createspms','createspms.id','trscale.spmID')->join('create_t_m_s','create_t_m_s.id','createspms.tiketID')->whereNotNull('netto')->whereNotNull('createspms.sealNo1')->where('create_t_m_s.tmQtyKg','>',0)->orderBy('jam_out','desc')->paginate(10);
        // dd($dataout);
        return view('livewire.fgdashboard', ['datafgtruk' => $data, 'data7hari' => $data7hari, 'datatrukout' => $dataout, 'antrianskr' => $antrianskr, 'antrianbsk' => $antrianbsk, 'registered' => $registrasi   ]);
    }
}
