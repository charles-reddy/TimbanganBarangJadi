<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\JembatanTimbang;
use App\Models\Product;
use App\Models\Transporter;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class Uploadappkarung extends Component
{
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';
    public $sortColumn = 'jam_in';
    public $sortDirection = 'desc';
    public $driver;
    public $carID;
    public $doNo;
    public $poNo;
    public $remarks;
    public $custID;
    public $spmID;
    public $spmNo;
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
    public $avgKarung;
    #[Rule('max:1024', message: 'Foto Bukti 1 maks 1 MB')]
    #[Validate('required', message: 'Silahkan Upload foto Bukti 1')]
    #[Validate('image', message: 'Bukti 1 harus image')]
    public $buktiAppKarung1;
    #[Validate('required', message: 'Silahkan Upload foto Bukti 2')]
    #[Rule('max:1024', message: 'Foto Bukti 2 maks 1 MB')]
    #[Validate('image', message: 'Bukti 2 harus image')]
    public $buktiAppKarung2;
    #[Rule('max:1024', message: 'Foto Bukti 3 maks 1 MB')]
    #[Rule('max:1024', message: 'Foto Bukti 3 maks 1 MB')]
    #[Validate('image', message: 'Bukti 3 harus image')]
    public $buktiAppKarung3;


    
    public function update()
    {
        
        $tgl = Carbon::now();
        $userIDIN = Auth::user()->id;
        $usernameIN = Auth::user()->username;
        $Bukti = str_replace("/","-",$this->spmNo);
        $buktiApp1 = $Bukti . '-1.jpg';
        $buktiApp2 = $Bukti . '-2.jpg';
        $buktiApp3 = $Bukti . '-3.jpg';
       
        $this->validate();
         
        try {
               
                // DB::connection('sqlsrv')->table('trscale')->where('id',$this->transID)->update([
                //     'isApp' => 1,
                //     'isAppID' => $userIDIN,
                //     'isAppDate' => $tgl,
                    
                // ]);

                

                DB::connection('sqlsrv')->table('createspms')->join('trscale','trscale.spmID','createspms.id' )->where('createspms.id',$this->spmID)->update([
                    
                    'buktiAppKarung1' => 'uploads/appkarung/' . $buktiApp1,
                    'buktiAppKarung2' => 'uploads/appkarung/' . $buktiApp2,
                    'buktiAppKarung3' => 'uploads/appkarung/' . $buktiApp3,
                    
                ]);

                if($this->buktiAppKarung1){
                    $this->buktiAppKarung1->storeAs('uploads/appkarung',$buktiApp1,'public');
                };

                if($this->buktiAppKarung2){
                    $this->buktiAppKarung2->storeAs('uploads/appkarung',$buktiApp2,'public');
                };

                if($this->buktiAppKarung3){
                    $this->buktiAppKarung3->storeAs('uploads/appkarung',$buktiApp3,'public');
                };
                
                session()->flash('message', 'Data berhasil dimasukkan');
                $this->clear();
                redirect('/uploadappkarung');
                

        } catch (Exception $e) {
            
            // throw $e;
            session()->flash('error', 'failed to store data');
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
        // $this->isApp = '';
        redirect('/uploadappkarung');
    }



    public function edit($id)
    {   
       
        // $data = Trscale::find($id);
        $data = DB::connection('sqlsrv')->table('createspms')->join('trscale', 'trscale.spmID', 'createspms.id')->where('trscale.spmID',$id)->first();
        // dd($data);
        $this->driver = $data->driver;
        $this->carID = $data->carID;
        $this->custID = $data->custID;
        $this->doNo = $data->doNo;
        $this->avgKarung = number_format($data->avgKarung,2);
        $this->b10QtyKarung = $data->b10QtyKarung;
        $this->transID = $id;
        $custN = Customer::where('custID', $this->custID)->value('custName');
        $this->custName = $custN;
        // // $this->transpID = $data->transpID;
        // // $transpN = Transporter::where('transpID', $this->transpID)->value('transpName');
        // $this->transpName = $transpN;
        $this->itemCode = $data->itemCode;
        $itemC = Product::where('itemCode', $this->itemCode)->value('ItemName');
        $this->itemName = $itemC;
        $this->updateData = true;
        $this->id_trscale = $id;
        $this->spmID = $data->spmID;
        $this->spmNo = $data->spmNo;

    }


    public function render()
    {
        
        $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->join('createspms','createspms.id', 'trscale.spmID')->where('type','<>','FG-L')->whereNotNull('avgKarung')->where('isApp',false)->whereNull('BuktiAppKarung1')->whereBetween('trscale.created_at',[Carbon::now()->addDays(-3), Carbon::now()  ])->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        // dd($data);
        $timbangan = JembatanTimbang::all();
        $pelanggan = Customer::all();
        $angkutan = Transporter::all();
        $barang = Product::all();
        return view('livewire.uploadappkarung', ['datascale' => $data, 'customer' => $pelanggan, 'transporter' => $angkutan, 'product' => $barang, 'timbangan' => $timbangan]);
    }
}
