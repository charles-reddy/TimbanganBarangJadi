<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\JembatanTimbang;
use App\Models\Product;
use App\Models\Transporter;
use App\Models\Trscale;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Inputkarung extends Component
{
    protected $paginationTheme = 'bootstrap';
    public $sortColumn = 'jam_in';
    public $sortDirection = 'desc';
    public $driver;
    public $carID;
    public $doNo;
    public $poNo;
    public $remarks;
    public $custID;
    public $transpID;
    public $itemCode;
    public $updateData = false;
    public $id_trscale;
    public $katakunci;
    public $katakunciout;
    public $trscaleSelectedID = [];
    public $selected ='';
    public $custName;
    public $transpName;
    public $itemName;
    public $jembatanTimbang;
    public $timbangin;
    public $timbanganID;
    public $timbangout;
    public $transID;
    public $netto; 
    public $timbanganoutID;
    public $custN;
    public $jam_out;
    public $userIDOUT;
    public $usernameOUT;
    #[Validate('required', message: 'qty Karung harus diisi')]
    #[Validate('integer', message: 'qty Karung harus dalam angka')]
    public $b10QtyKarung;
    #[Validate('required', message: 'no kontainer harus diisi')]
    public $kontainerNo;
    public $spmID;
    #[Validate('required', message: 'Bacth No harus diisi')]
    public $b10BatchNo;
    

    public function update()
    {
        
        $tgl = Carbon::now();
        $userIDIN = Auth::user()->id;
        $usernameIN = Auth::user()->username;
       
        $this->validate();
         
        try {
                $tes=DB::connection('sqlsrv')->table('trscale')->where('id',$this->transID)->get();
            //    dd($tes);
                DB::connection('sqlsrv')->table('trscale')->where('id',$this->transID)->update([
                    'b10QtyKarung' => $this->b10QtyKarung,
                    'b10BatchNo' => $this->b10BatchNo,
                    
                ]);

                DB::connection('sqlsrv')->table('createspms')->join('trscale','trscale.spmID','createspms.id' )->where('createspms.id',$this->spmID)->update([
                    'kontainerNo' => $this->kontainerNo,
                    
                ]);

                
                
                session()->flash('message', 'Data berhasil dimasukkan');
                $this->clear();
                redirect('/inputkarung');
                

        } catch (Exception $e) {
            
            throw $e;
            // session()->flash('error', 'failed to store data');
            return;
        }

    }

    public function clear()
    {
        $this->driver = '';
        
        $this->carID = '';
        $this->itemCode  = '';
        $this->custID = '';
        $this->transpID = '';
        $this->itemCode = '';
        $this->doNo = '';
        $this->b10QtyKarung = '';
        $this->b10BatchNo = '';
        redirect('/inputkarung');
    }



    public function edit($id)
    {   
       
        // $data = Trscale::find($id);

        // dd($id);
        $data = DB::connection('sqlsrv')->table('createspms')->join('trscale', 'trscale.spmID', 'createspms.id')->where('trscale.id',$id)->first();
        // dd($data);
        $this->driver = $data->driver;
        $this->carID = $data->carID;
        $this->custID = $data->custID;
        $this->doNo = $data->doNo;
        
        $this->transID = $id;
        $custN = Customer::where('custID', $this->custID)->value('custName');
        $this->custName = $custN;
        // $this->transpID = $data->transpID;
        // $transpN = Transporter::where('transpID', $this->transpID)->value('transpName');
        // $this->transpName = $transpN;
        $this->itemCode = $data->itemCode;
        $itemC = Product::where('itemCode', $this->itemCode)->value('ItemName');
        $this->itemName = $itemC;
        $this->updateData = true;
        $this->id_trscale = $id;
        $this->kontainerNo = $data->kontainerNo;
        $this->spmID = $data->spmID;

       
        
       
        
    }

    public function sort($columnName)
    {
        $this->sortColumn = $columnName;
        $this->sortDirection = $this->sortDirection == 'asc'?'desc' : 'asc';
        
    }

    public function render()
    {
        if (($this->katakunci or $this->katakunciout)  !=null) {
            // $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunci . '%')->whereNull('netto')->wherenull('b10QtyKarung')->orwhere('carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunci . '%')->whereNull('netto')->wherenull('b10QtyKarung')->orwhere('carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            // dd($data);
        } else {
            // $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->join('createspms','createspms.id','trscale.spmID' )->wherenull('netto')->wherenull('b10QtyKarung')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            $data = DB::connection('sqlsrv')->table('createspms')->join('trscale', 'trscale.spmID', 'createspms.id')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->wherenull('netto')->wherenull('b10QtyKarung')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        
            //    dd($data);
        }

        
        $timbangan = JembatanTimbang::all();
        $pelanggan = Customer::all();
        $angkutan = Transporter::all();
        $barang = Product::all();
        return view('livewire.inputkarung' , ['datascale' => $data, 'customer' => $pelanggan, 'transporter' => $angkutan, 'product' => $barang, 'timbangan' => $timbangan]); 
    }
}
