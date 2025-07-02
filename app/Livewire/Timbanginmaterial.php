<?php

namespace App\Livewire;

use App\Models\JembatanTimbang;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\WithPagination;

class Timbanginmaterial extends Component
{
    use WithPagination;
    use WithFileUploads;
    public $sortColumn = 'jam_reg';
    public $sortDirection = 'desc';
    public $regNo;
    public $driver;
    public $carID;
    public $suppID;
    public $itemCode;
    public $doNo;
    public $updateData = false;
    public $jembatanTimbang;
    #[Validate('required', message: 'berat kosong')]
    public $timbangin;
    #[Validate('required', message: 'pilih timbangan')]
    public $timbanganID;
    public $jam_in;
    public $userIDIN; 
    public $usernameIN;
    public $transID;
    public $remarks;
    public $id_trscale;
    public $katakunci;
    public $trscaleSelectedID = [];
    public $poNo;
    
    


    public function timbang()
    {
        $this->timbangin = '';
        try {

             $iptimbangan = JembatanTimbang::where('timbanganID', '=',$this->timbanganID)->value('IP');
       // *************** testing timbangan *******************
                // $this->timbangin = 88888;
                
                // // dd($this->timbanganID);
                
                // if ($this->timbanganID == 1) {
                // //     // dd('10');
                //     $data = "http://10.20.1.49:3000/api/weight/SCALE_10";
                // } elseif ($this->timbanganID == 2) {
                // //     // dd('9');
                //     $data = "http://10.20.1.49:3000/api/weight/SCALE_09";
                // } else {
                // //     // dd('8');
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
        
        //  dd($this->output, $this->cctv);
        $this->validate();
         
        try {
                
                $this->jam_in = $jam_in;
                $this->userIDIN = $userIDIN; 
                $this->usernameIN = $usernameIN;
                
                // dd($this->custID);
                DB::connection('sqlsrv')->table('trscaleb19s')->where('id',$this->transID)->update([
                    
                    'remarks' => $this->remarks,
                    'timbangin' => $this->timbangin,
                    'timbanganInID' => $this->timbanganID,
                    'jam_in' => $this->jam_in,
                    'userIDIN' => $this->userIDIN,
                    'usernameIN' => $this->usernameIN,
                    'updated_at' => $this->jam_in,
                    
                ]);
                 
                
                
                session()->flash('message', 'Data berhasil dimasukkan');
                $this->clear();
                redirect('/timbanginmaterial');
                

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
        $this->doNo = '';
        $this->poNo = '';
        $this->remarks = '';
        $this->itemCode = '';  
        $this->updateData = false;
        $this->id_trscale = '';
        $this->trscaleSelectedID = [];
        $this->timbangin = '';
        
       
        
        redirect('/timmasuk');
    }



    #[Computed()]
    public function regdata()
    {
        // $dataspm = DB::connection('sqlsrv')->table('createspms')->join('customers', 'customers.custID', 'createspms.custID')->join('transporters', 'transporters.transpID', 'createspms.transpID')->join('products', 'products.itemCode', 'createspms.itemCode')->where('id',$this->spmNo)->first();
        $registered = DB::connection('sqlsrv')->table('trscaleb19s')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->join('products', 'products.itemCode', 'trscaleb19s.itemCode')->where('id',$this->regNo)->first();
        
        //  dd($registered);
         $this->driver = $registered->driver;
         $this->carID = $registered->carID;
         $this->suppID = $registered->suppID .'-'. $registered->suppName;
        //  $this->transpID = $registered->transpID .'-'. $registered->transpName; 
         $this->itemCode = $registered->itemCode .'-'. $registered->itemName;
         $this->doNo = $registered->doNo;
         $this->transID = $registered->id;
    }

    public function render()
    {
        if ($this->katakunci !=null) {
            $data = DB::connection('sqlsrv')->table('trscaleb19s')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->join('products', 'products.itemCode', 'trscaleb19s.itemCode')->whereNull('netto')->where('suppName','like','%' . $this->katakunci . '%')->orwhere('carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        } else {
            $data = DB::connection('sqlsrv')->table('trscaleb19s')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->join('products', 'products.itemCode', 'trscaleb19s.itemCode')->whereNull('netto')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        }

        if ($this->regNo !=null)
        {
           
            $this->regdata(); 
        }
        $timbangan = JembatanTimbang::all();

        $reglist = DB::connection('sqlsrv')->table('trscaleb19s')->select('trscaleb19s.id','trscaleb19s.carID','suppliers.suppName','suppliers.suppID')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->wherenull('timbangin')->get();

        return view('livewire.timbanginmaterial', ['datatim' => $data, 'datareg1' => $reglist, 'timbangan' => $timbangan]);
    } 
}
