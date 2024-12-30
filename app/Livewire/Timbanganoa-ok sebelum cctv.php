<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\JembatanTimbang;
use App\Models\Product;
use App\Models\Transporter;
use App\Models\Trscale;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PhpParser\Node\Stmt\TryCatch;
use Ramsey\Uuid\Type\Integer;

class Timbanganoa extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';
    #[Validate('required', message: 'driver kosong')]
    public $driver;
    #[Validate('required', message: 'Plat No kosong')]
    public $carID;
    #[Validate('required', message: 'DO kosong')]
    public $doNo;
    public $poNo;
    public $remarks;
    #[Validate('required', message: 'customer kosong')]
    public $custID;
    // #[Validate('required', message: 'transporter kosong')]
    public $transpID;
    #[Validate('required', message: 'item barang kosong')]
    public $itemCode;
    public $updateData = false;
    public $id_trscale;
    public $katakunci;
    public $trscaleSelectedID = [];
    public $sortColumn = 'jam_in';
    public $sortDirection = 'desc';
    public $selected ='';
    public $custName;
    public $transpName;
    public $itemName;
    public $jembatanTimbang;
    #[Validate('required', message: 'berat kosong')]
    public $timbangin;
    #[Validate('required', message: 'pilih timbangan')]
    public $timbanganID;
    public $jam_in;
    public $userIDIN;
    public $usernameIN;
    public $spmNo;
    public $output;
    public $cctv;
    
    
    
    public function timbang()
    {
        $this->timbangin = '';
        try {

        //      $iptimbangan = JembatanTimbang::where('timbanganID', '=',$this->timbanganID)->value('IP');
       // *************** testing timbangan *******************
                // $this->timbangin = 88888;
                
                // // dd($this->timbanganID);
                
                // if ($this->timbanganID == 1) {
                //     // dd('10');
                //     $data = "http://10.20.1.49:3000/api/weight/SCALE_10";
                // } elseif ($this->timbanganID == 2) {
                //     // dd('9');
                //     $data = "http://10.20.1.49:3000/api/weight/SCALE_09";
                // } else {
                //     // dd('8');
                //     $data = "http://10.20.1.49:3000/api/weight/SCALE_08";
                // }

                // dd($this->output);
        // *************** testing timbangan *******************

                switch ($this->timbanganID) {
                    case 1:
                        $data = "http://10.20.1.49:3000/api/weight/SCALE_10";
                        break;
                    
                    case '2':
                        $data = "http://10.20.1.49:3000/api/weight/SCALE_09";
                        break;   

                    case '3':
                        $data = "http://10.20.1.49:3000/api/weight/SCALE_08";
                        break; 

                    default:
                        
                        break;
                }
                
                

                $client= new Client();
                // $data = "http://10.20.1.49:3000/api/weight/SCALE_09";
                $response = $client->request('GET',$data);
                $content =  $response->getBody()->getContents();
                $contentarray = json_decode($content,true);
            //    dd($contentarray['weight']);
                 $this->timbangin = $contentarray['weight'];
        } catch (Exception $e) {
            session()->flash('error', 'Pastikan Timbangan yg dipilih sesuai');
            return;
        }
        
       
    }
    

    public function store()
    {
        
        $jam_in = Carbon::now();
        $userIDIN = Auth::user()->id;
        $usernameIN = Auth::user()->username;
        $cid = explode("-",$this->custID);
        $tid = explode("-",$this->transpID);
        $bid = explode("-",$this->itemCode);
        
        //  dd($this->output, $this->cctv);
        $this->validate();
         
        try {
                
                $this->jam_in = $jam_in;
                $this->userIDIN = $userIDIN; 
                $this->usernameIN = $usernameIN;
                $this->custID = $cid[0];
                $this->transpID = $tid[0];
                $this->itemCode = $bid[0];
                // dd($this->custID);
                DB::connection('sqlsrv')->table('trscale')->insert([
                    'driver' => $this->driver,
                    'carID' => $this->carID,
                    'custID' => $this->custID,
                    'transpID' => $this->transpID,
                    'itemCode' => $this->itemCode,
                    'doNo' => $this->doNo,
                    'poNo' => $this->poNo,
                    'remarks' => $this->remarks,
                    'timbangin' => $this->timbangin,
                    'timbanganID' => $this->timbanganID,
                    'jam_in' => $this->jam_in,
                    'userIDIN' => $this->userIDIN,
                    'usernameIN' => $this->usernameIN,
                    'spmID' => $this->spmNo,
                    'created_at' => $this->jam_in,
                    
                ]);
                
                
                DB::connection('sqlsrv')->table('createspms')->where('id',$this->spmNo)->update([
                    'isIN' => true,
                    
                
                ]);
                // $rules = [
                //     'driver' => 'required',
                //     'carID' => 'required',
                //     'custID' => 'required',
                //     'transpID' => 'required',
                //     'itemCode' => 'required',
                //     'doNo' => 'nullable',
                //     'poNo' => 'nullable',
                //     'remarks' => 'nullable',
                //     'timbangin' => 'required',
                //     'timbanganID' => 'required',
                //     'jam_in' => 'required',
                //     'userIDIN' => 'required',
                //     'usernameIN' => 'required',
                    
                // ];
            
                // $pesan = [
                //     'driver.required' => 'driver wajib diisi',
                //     'carID.required' => 'car id wajib diisi',
                //     'custID.required' => 'customer ID wajib diisi',
                //     'transpID.required' => 'transporter ID wajib diisi',
                //     'itemCode.required' => 'item Code wajib diisi',
                //     'timbangin.required' => 'Data timbang kosong',
                //     'timbanganID.required' => 'Pilih ID Timbangan',
                // ];
                
                // $validated = $this->validate($rules, $pesan);
                // dd($validated);
                // Trscale::create($validated);
                session()->flash('message', 'Data berhasil dimasukkan');
                $this->clear();
                redirect('/timmasuk');
                

        } catch (Exception $e) {
            
            throw $e;
            // session()->flash('error', 'failed to store data');
            return;
        }

    }


    public function edit($id)
    {
        
        $data = Trscale::find($id);
        $this->driver = $data->driver;
        $this->carID = $data->carID;
        $this->custID = $data->custID;
        $this->doNo = $data->doNo;
        $this->poNo = $data->poNo;
        $this->remarks = $data->remarks;
        
        $custN = Customer::where('custID', $this->custID)->value('custName');
        $this->custName = $custN;
        $this->transpID = $data->transpID;
        $transpN = Transporter::where('transpID', $this->transpID)->value('transpName');
        $this->transpName = $transpN;
        $this->itemCode = $data->itemCode;
        $itemC = Product::where('itemCode', $this->itemCode)->value('ItemName');
        $this->itemName = $itemC;
        $this->updateData = true;
        $this->id_trscale = $id;
        
       
        
    }

    public function update()
    {
        try {
            $rules = [
                'driver' => 'required',
                'carID' => 'required|max:10',
                'custID' => 'required',
                'transpID' => 'required',
                'itemCode' => 'required',
                'doNo' => 'nullable',
                'poNo' => 'nullable',
                'remarks' => 'nullable'
            ];
            $pesan = [
                'driver.required' => 'driver wajib diisi',
                'carID.required' => 'car id wajib diisi',
                'carID.max' => 'carid data max 10 digit',
                'custID.required' => 'customer Id wajib diisi',
                'transpID.required' => 'transporter Id wajib diisi',
                'itemCode.required' => 'item Code wajib diisi',
                
            ];
            $validated = $this->validate($rules, $pesan);
            $data = Trscale::find($this->id_trscale);
            $data->update($validated);
            session()->flash('message', 'Data berhasil diperbaharui');
            $this->clear();
        } catch (Exception $e) {
            session()->flash('error', 'failed to update data');
            return;
        }
       
    }

    public function clear()
    {
        $this->driver = '';
        $this->carID = '';
        $this->doNo = '';
        $this->poNo = '';
        $this->remarks = '';
        $this->transpID = '';
        $this->itemCode = '';  
        $this->updateData = false;
        $this->id_trscale = '';
        $this->trscaleSelectedID = [];
        $this->timbangin = '';
        
       
        
        redirect('/timmasuk');
    }

    public function delete()
    {
        try {
            if ($this->id_trscale !=''){
                $id = $this->id_trscale;
                Trscale::find($id)->delete();
            }
    
            if (count($this->trscaleSelectedID)){
                for($x = 0; $x < count($this->trscaleSelectedID);$x++){
                    Trscale::find($this->trscaleSelectedID[$x])->delete();
                }
            }
            session()->flash('message', 'Data berhasil dihapus');
            $this->clear();
           
        } catch (Exception $e) {
            session()->flash('error', 'failed to delete data');
            return;
        }
      
    }

    public function deleteConfirmation($id)
    {
        if($id !='') {
            $this->id_trscale =  $id;
        } 
    }

    public function sort($columnName)
    {
        $this->sortColumn = $columnName;
        $this->sortDirection = $this->sortDirection == 'asc'?'desc' : 'asc';

    }

   #[Computed()]
    public function spmdata()
    {
        // $dataspm = DB::connection('sqlsrv')->table('createspms')->join('customers', 'customers.custID', 'createspms.custID')->join('transporters', 'transporters.transpID', 'createspms.transpID')->join('products', 'products.itemCode', 'createspms.itemCode')->where('id',$this->spmNo)->first();
        $dataspm = DB::connection('sqlsrv')->table('createspms')->join('customers', 'customers.custID', 'createspms.custID')->join('products', 'products.itemCode', 'createspms.itemCode')->where('id',$this->spmNo)->first();
        
        // dd($dataspm);
         $this->driver = $dataspm->driver;
         $this->carID = $dataspm->carID;
         $this->custID = $dataspm->custID .'-'. $dataspm->custName;
        //  $this->transpID = $dataspm->transpID .'-'. $dataspm->transpName;
         $this->itemCode = $dataspm->itemCode .'-'. $dataspm->itemName;
         $this->doNo = $dataspm->sppbNo;
    }
     

    public function render()
    {   
        if ($this->katakunci !=null) {
            // $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunci . '%')->whereNull('netto')->orwhere('carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunci . '%')->whereNull('netto')->orwhere('carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
          
        } else {
            // $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->whereNull('netto')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->whereNull('netto')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            
            // dd($data);
           
        }
        
        if ($this->spmNo !=null)
        {
            $this->spmdata();
        }
       

        $timbangan = JembatanTimbang::all();
        $pelanggan = Customer::all();
        $angkutan = Transporter::all();
        $barang = Product::where('itemName','like','%gkr%')->orwhere('itemName','like','%mola%')->get();
        $spmlist = DB::connection('sqlsrv')->table('createspms')->select('id','spmNo')->where('isIN',0)->get();
        // dd($spmlist);
        return view('livewire.timbanganoa', ['datascale' => $data, 'dataspm' => $spmlist, 'customer' => $pelanggan, 'transporter' => $angkutan, 'product' => $barang, 'timbangan' => $timbangan]); 
    }
}
