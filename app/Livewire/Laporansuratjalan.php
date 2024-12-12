<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\JembatanTimbang;
use App\Models\Product;
use App\Models\Transporter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Laporansuratjalan extends Component
{
    use WithPagination;
    public $katakunciout;
    public $sortColumn = 'tglSpm';
    public $sortDirection = 'desc';
    public $trscaleSelectedID = [];
    public $tglin ;
    
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
            $sdhout = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->join('createspms','createspms.id', 'trscale.spmID')->where('trscale.driver','like','%' . $this->katakunciout . '%')->orwhere('trscale.carID','like','%' . $this->katakunciout . '%')->wheredate('trscale.jam_in','>=',$this->tglin)->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            
        } elseif (($this->tglin  )  !=null) {
            // dd('dua'); 
            $sdhout = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->join('createspms','createspms.id', 'trscale.spmID')->wheredate('trscale.jam_in','>=',$this->tglin)->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        } else {
             
            $this->tglin = $tglawal;
            // dd($tglawal, $this->jam_in);
            // $sdhout = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->join('createspms','createspms.id', 'trscale.spmID')->join('createsppbs','createsppbs.id', 'trscale.doNo')->wheredate('trscale.jam_in','>=',$this->tglin)->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            $sdhout = DB::connection('sqlsrv')->table('createspms')->join('customers', 'customers.custID', 'createspms.custID')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            //  dd($sdhout);
        
        }

       
        $timbangan = JembatanTimbang::all();
        $pelanggan = Customer::all();
        $angkutan = Transporter::all();
        $barang = Product::all();



        return view('livewire.laporansuratjalan',['datascaleout' => $sdhout, 'customer' => $pelanggan, 'transporter' => $angkutan, 'product' => $barang, 'timbangan' => $timbangan]);
    }
}
