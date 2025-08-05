<?php

namespace App\Livewire;

use App\Exports\exportTrukTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Truktransaction extends Component
{
    use WithPagination;
    public $katakunci;
    public $tglout;


    public function export_out()
    {
        
        return Excel::download(new exportTrukTransaction($this->tglout, $this->katakunci), "Truktransaction-export.xlsx");
    } 


     public function clear()
    {
        
        redirect('/truktransaction');
    }

    public function render()
    {
        $tglout = DB::connection('sqlsrv')->table('vw_truktransaction')->whereNotNull('netto')->orderBy('id','desc')->first();
        // dd($tglout);
        if ($this->katakunci !=null) {
            $data = DB::connection('sqlsrv')->table('vw_truktransaction')->whereNotNull('netto')->where('carID','like','%' . $this->katakunci . '%')->orderBy('id', 'desc')->paginate(10);
            

        } elseif (($this->tglout  )  !=null) {
            $data = DB::connection('sqlsrv')->table('vw_truktransaction')->wheredate('tgl','=',$this->tglout)->whereNotNull('netto')->orderBy('id', 'desc')->paginate(10);
        } else {
            $this->tglout = $tglout->tgl;
            // dd($tglout->tgl);
             $data = DB::connection('sqlsrv')->table('vw_truktransaction')->whereNotNull('netto')->whereBetween('tgl',[ Carbon::now()->addDays(-14), Carbon::now() ])->orderBy('id', 'desc')->paginate(10);
       
        }
        // dd($data);
        return view('livewire.truktransaction',['data' => $data]);
    }
}
