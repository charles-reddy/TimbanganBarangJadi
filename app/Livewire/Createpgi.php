<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\WithPagination;

class Createpgi extends Component
{
    use WithFileUploads;
    use WithPagination;
    public $katakunci;
    public $driver;
    public $carID;
    public $spmID;
    public $spmNo;
    public $custName;
    public $custID;
    public $doNo;
    public $itemName;
    public $timbangin;
    #[Validate('required', message: 'Pilih kendaraan Muat dari Customer')]
    public $transID;
    #[Rule('max:1024', message: 'Foto PGI maks 1 MB')]
    #[Validate('required', message: 'Silahkan Upload foto PGI')]
    #[Validate('image', message: 'PGI harus image')]
    public $buktiPGI;
    public $itemCode;
    public $updateData;
    public $id_trscale;
    public $kontainerNo;


    public function update()
    {
        
        $tgl = Carbon::now();
        $userIDIN = Auth::user()->id;
        $usernameIN = Auth::user()->username;
        $replacespm = str_replace("/","-",$this->spmNo);
        $replacespm1 = $replacespm . '.jpg';
        // dd($replacespm);
        
       
        $this->validate();
         
        try {
               
            // dd($this->spmID);

                DB::connection('sqlsrv')->table('createspms')->join('trscale','trscale.spmID','createspms.id' )->where('createspms.id',$this->spmID)->update([
                    
                    'buktiPGI' => 'uploads/pgi/' . $replacespm1,
                    
                ]);

                if($this->buktiPGI){
                    $this->buktiPGI->storeAs('uploads/pgi',$replacespm1,'public');
                }
                
                session()->flash('message', 'Data berhasil dimasukkan');
                $this->clear();
                redirect('/createpgi');
                

        } catch (Exception $e) {
            
            // throw $e;
            session()->flash('error', 'failed to store data');
            return;
        }

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
        $this->spmNo = $data->spmNo;
       
        
    }

    public function clear()
    {
        
        redirect('/createpgi');
    }
    
    public function render()
    {
        
        $sdhout = DB::connection('sqlsrv')->table('trscale')->join('createspms', 'createspms.id', 'trscale.spmID')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'trscale.itemCode')->join('customers', 'customers.custID', 'trscale.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->whereNull('buktiPGI')->whereNotNull('netto')->whereNotNull('createspms.sealNo1')->select('createspms.id as spmID','createspms.sealNo1','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type', 'trscale.id as trsID', 'trscale.jam_out', 'trscale.timbangin', 'trscale.timbangout', 'trscale.netto', 'trscale.avgkarung', 'createspms.sealNo', 'createsppbs.sppbNo', 'create_t_m_s.pendfNo', 'createspms.buktiPGI' )->orderBy('spmID', 'desc')->paginate(10);
        // dd($sdhout);   
        return view('livewire.createpgi',['sdhout' => $sdhout]);
    }
}
