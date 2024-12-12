<?php

namespace App\Livewire;

use App\Exports\ExportTimbangOut;
use App\Models\Customer;
use App\Models\JembatanTimbang;
use App\Models\Product;
use App\Models\Transporter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Laporantimbangan extends Component
{
    use WithPagination;
    public $katakunciout;
    public $sortColumn = 'jam_in';
    public $sortDirection = 'desc';
    public $trscaleSelectedID = [];
    public $tglin ;

    public function export_out()
    {
        
        return Excel::download(new ExportTimbangOut($this->tglin, $this->katakunciout), "timbanganexport.xlsx");
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
            $sdhout = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunciout . '%')->whereNotNull('netto')->orwhere('carID','like','%' . $this->katakunciout . '%')->wheredate('jam_in','>=',$this->tglin)->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        
            

            
        } elseif (($this->tglin  )  !=null) {
            // dd('dua'); 
            $sdhout = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->wheredate('jam_in','>=',$this->tglin)->whereNotNull('netto')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        } else {
             
            $this->tglin = $tglawal;
            // dd($tglawal, $this->jam_in);
            $sdhout = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->wheredate('jam_in','>=',$this->tglin)->whereNotNull('netto')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        
        }

       
        $timbangan = JembatanTimbang::all();
        $pelanggan = Customer::all();
        $angkutan = Transporter::all();
        $barang = Product::all();
       
        return view('livewire.laporantimbangan',['datascaleout' => $sdhout, 'customer' => $pelanggan, 'transporter' => $angkutan, 'product' => $barang, 'timbangan' => $timbangan]);
    }
} 
