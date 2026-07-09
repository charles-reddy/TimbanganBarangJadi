<?php

namespace App\Livewire;

use App\Exports\ExportTimbangOut;
use App\Exports\ExportTimbangOutFG;
use App\Models\Customer;
use App\Models\JembatanTimbang;
use App\Models\Product;
use App\Models\Transporter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Lappemuatanfg extends Component
{
    use WithPagination;
    public $katakunciout;
    public $sortColumn = 'jam_in';
    public $sortDirection = 'desc';
    public $trscaleSelectedID = [];
    public $tglin ;


    public function export_out()
    {
        
        return Excel::download(new ExportTimbangOutFG($this->tglin, $this->katakunciout), "timbanganexport-FG.xlsx");
    } 
    
    public function sort($columnName)
    {
        $this->sortColumn = $columnName;
        $this->sortDirection = $this->sortDirection == 'asc'?'desc' : 'asc'; 
        
    }

    public function clear()
    {
        $this->katakunciout = '';
        $this->tglin = '';
    }



    public function render()
    {

        $tglawal=date('m-d-Y',strtotime(Carbon::now()->subDay(4)));
        

        if (($this->katakunciout )  !=null) {
            
            //  dd('satu');   
            $sdhout = DB::connection('sqlsrv')->table('trscale')
                ->join('customers', 'customers.custID', 'trscale.custID')
                ->join('products', 'products.itemCode', 'trscale.itemCode')
                ->leftJoin('createspms', 'createspms.id', 'trscale.spmID')
                ->leftJoin('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
                ->select('trscale.*', 'customers.custName', 'products.itemName', 'create_t_m_s.tmTranspName', 'createspms.sealNo', 'create_t_m_s.jamMuat',
                    DB::raw("CASE 
                        WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' 
                        WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' 
                        WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' 
                        ELSE 'Luar Jam Shift' 
                    END as shift_tiket")
                )
                ->where('driver','like','%' . $this->katakunciout . '%')
                ->whereNotNull('netto')
                ->orwhere('carID','like','%' . $this->katakunciout . '%')
                ->wheredate('jam_in','>=',$this->tglin)
                ->orderby($this->sortColumn ,$this->sortDirection)
                ->paginate(5);
        
            
 
            
        } elseif (($this->tglin  )  !=null) {
            // dd('dua'); 
            $sdhout = DB::connection('sqlsrv')->table('trscale')
                ->join('customers', 'customers.custID', 'trscale.custID')
                ->join('products', 'products.itemCode', 'trscale.itemCode')
                ->leftJoin('createspms', 'createspms.id', 'trscale.spmID')
                ->leftJoin('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
                ->select('trscale.*', 'customers.custName', 'products.itemName', 'create_t_m_s.tmTranspName', 'createspms.sealNo', 'create_t_m_s.jamMuat',
                    DB::raw("CASE 
                        WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' 
                        WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' 
                        WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' 
                        ELSE 'Luar Jam Shift' 
                    END as shift_tiket")
                )
                ->wheredate('jam_in','>=',$this->tglin)
                ->whereNotNull('netto')
                ->orderby($this->sortColumn ,$this->sortDirection)
                ->paginate(5);
        } else {
             
            $this->tglin = $tglawal;
            // dd($tglawal, $this->jam_in);
            $sdhout = DB::connection('sqlsrv')->table('trscale')
                ->join('customers', 'customers.custID', 'trscale.custID')
                ->join('products', 'products.itemCode', 'trscale.itemCode')
                ->leftJoin('createspms', 'createspms.id', 'trscale.spmID')
                ->leftJoin('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
                ->select('trscale.*', 'customers.custName', 'products.itemName', 'create_t_m_s.tmTranspName', 'createspms.sealNo', 'create_t_m_s.jamMuat',
                    DB::raw("CASE 
                        WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' 
                        WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' 
                        WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' 
                        ELSE 'Luar Jam Shift' 
                    END as shift_tiket")
                )
                ->whereNotNull('netto')
                ->orderby($this->sortColumn ,$this->sortDirection)
                ->paginate(5);
        
        }
       
        $timbangan = JembatanTimbang::all();
        $pelanggan = Customer::all();
        $angkutan = Transporter::all();
        $barang = Product::all();
       
        return view('livewire.lappemuatanfg',['datascaleout' => $sdhout, 'customer' => $pelanggan, 'transporter' => $angkutan, 'product' => $barang, 'timbangan' => $timbangan]);
    }
}
