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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use PhpParser\Node\Stmt\TryCatch;

class Timbanganoa extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
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
    public $trscaleSelectedID = [];
    public $sortColumn = 'jam_in';
    public $sortDirection = 'desc';
    public $selected ='';
    public $custName;
    public $transpName;
    public $itemName;
    public $jembatanTimbang;
    public $timbangin;
    public $timbanganID;
    public $jam_in;
    public $userIDIN;
    public $usernameIN;
    
    
    
    public function timbang()
    {
        $this->timbangin = '';
        try {

             // $iptimbangan = JembatanTimbang::where('timbanganID', '=',$this->timbanganID)->value('IP');
       
                // $this->timbangin = 8888;
                
                // dd($this->timbanganID);
                
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
        // dd($userIDIN);
        try {
            
                $this->jam_in = $jam_in;
                $this->userIDIN = $userIDIN; 
                $this->usernameIN = $usernameIN;

                $rules = [
                    'driver' => 'required',
                    'carID' => 'required',
                    'custID' => 'required',
                    'transpID' => 'required',
                    'itemCode' => 'required',
                    'doNo' => 'nullable',
                    'poNo' => 'nullable',
                    'remarks' => 'nullable',
                    'timbangin' => 'required',
                    'timbanganID' => 'required',
                    'jam_in' => 'required',
                    'userIDIN' => 'required',
                    'usernameIN' => 'required',
                    
                ];
            
                $pesan = [
                    'driver.required' => 'driver wajib diisi',
                    'carID.required' => 'car id wajib diisi',
                    'custID.required' => 'customer ID wajib diisi',
                    'transpID.required' => 'transporter ID wajib diisi',
                    'itemCode.required' => 'item Code wajib diisi',
                    'timbangin.required' => 'Data timbang kosong',
                    'timbanganID.required' => 'Pilih ID Timbangan',
                ];
                
                $validated = $this->validate($rules, $pesan);
                // dd($validated);
                Trscale::create($validated);
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

   
     

    public function render()
    {   
        if ($this->katakunci !=null) {
            $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunci . '%')->whereNull('netto')->orwhere('carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        } else {
            $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->whereNull('netto')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        }
        $timbangan = JembatanTimbang::all();
        $pelanggan = Customer::all();
        $angkutan = Transporter::all();
        $barang = Product::all();
        return view('livewire.timbanganoa', ['datascale' => $data, 'customer' => $pelanggan, 'transporter' => $angkutan, 'product' => $barang, 'timbangan' => $timbangan]); 
    }
}
