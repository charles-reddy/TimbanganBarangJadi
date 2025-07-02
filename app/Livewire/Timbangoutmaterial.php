<?php

namespace App\Livewire;

use App\Models\JembatanTimbang;
use App\Models\Product;
use App\Models\supplier;
use App\Models\Trscale;
use App\Models\trscaleb19;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\WithPagination;

class Timbangoutmaterial extends Component
{
    use WithPagination;
    use WithFileUploads;
    public $sortColumn = 'jam_in';
    public $sortDirection = 'desc';
    public $jembatanTimbang;
    public $timbangin;
    public $timbanganID;
    #[Validate('required', message: 'berat kosong')]
    public $timbangout;
    public $transID;
    public $netto;
    #[Validate('required', message: 'pilih timbangan')]
    public $timbanganoutID;
    public $driver;
    public $carID;
    public $suppID;
    public $doNo;
    public $poNo;
    public $remarks;
    public $suppName;
    public $suppN;
    public $updateData = false;
    public $itemName;
    public $id_trscale;
    public $katakunci;
    public $katakunciout;
    public $trscaleSelectedID = [];
    public $itemCode;
    public $jam_out;
    public $userIDOUT;
    public $usernameOUT;

    public function timbang()
    {
        $this->timbangout = '';
        $this->netto = '';
        try {
            switch ($this->timbanganoutID) {
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
                 $this->timbangout = $contentarray['weight'];
                 $this->timbangin;
        
            $this->netto = $this->timbangin - $this->timbangout; 
            if ($this->netto < 0)
            {
            
                $this->netto = $this->timbangout - $this->timbangin; 
            } 

        } catch (Exception $e) {
            session()->flash('error', 'Pastikan Timbangan yg dipilih sesuai');
            return;
        }
// *************** testing timbangan *******************
        // $iptimbangan = JembatanTimbang::where('timbanganID', '=',$this->timbanganoutID)->value('IP');
        // $this->timbangin;
        // $this->timbangout = 8888;
        // $this->netto = $this->timbangin - $this->timbangout; 
        // if ($this->netto < 0)
        // {
           
        //     $this->netto = $this->timbangout - $this->timbangin; 
        // } 
// *************** testing timbangan *******************  
     
       
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
        $this->timbangout = '';
        
        
        redirect('/timbangoutmaterial');
    }

    public function edit($id)
    {   
        $this->netto = '';
        $this->timbangout = '';
        $data = trscaleb19::find($id);
        $this->driver = $data->driver;
        $this->carID = $data->carID;
        $this->suppID = $data->suppID;
        $this->itemCode = $data->itemCode;
        $this->doNo = $data->doNo;
        $this->poNo = $data->poNo;
        $this->remarks = $data->remarks;
        $this->timbangin = $data->timbangin;
        $this->transID = $id;
        $suppN = supplier::where('suppID', $this->suppID)->value('suppName');
        $this->suppID = $suppN;
        $this->itemCode = $data->itemCode;
        $itemC = Product::where('itemCode', $this->itemCode)->value('ItemName');
        $this->itemCode = $itemC;
        $this->updateData = true;
        $this->id_trscale = $id;
    }

    

    public function store()
    {
        
        $userIDOUT = Auth::user()->id;
        $usernameOUT = Auth::user()->username;
        $this->validate();

        try {
            
                    
                        $this->jam_out = Carbon::now();
                        $this->userIDOUT = $userIDOUT; 
                        $this->usernameOUT = $usernameOUT;
                        DB::connection('sqlsrv')->table('trscaleb19s')->where('id',$this->transID)->update([
                            
                            'timbangout' => $this->timbangout,
                            'timbanganoutID' => $this->timbanganoutID,
                            'jam_out' => $this->jam_out,
                            'userIDOUT' => $this->userIDOUT,
                            'usernameOUT' => $this->usernameOUT,
                            'updated_at' => $this->jam_out,
                            'netto' => $this->netto,
                            
                        ]);
                        
                        
                        $data = Trscaleb19::find($this->id_trscale);
                        $id=$this->id_trscale;
                        $combineid = '/cetakoutm/'. $id ;
                        session()->flash('message', 'Data berhasil diperbaharui');
                        redirect($combineid);
                        // $this->clear();
                        //  redirect('/timbangoutmaterial');
                   
        } catch (Exception $e) {
            
            // session()->flash('error', 'failed to update data');
            throw $e;
            return;
        }
       
    }
    
    

    public function render()
    {
        if ($this->katakunci   !=null) {
            $data = DB::connection('sqlsrv')->table('trscaleb19s')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->join('products', 'products.itemCode', 'trscaleb19s.itemCode')->where('driver','like','%' . $this->katakunci . '%')->wherenotnull('timbangin')->wherenull('netto')->orwhere('carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            // $sdhout = DB::connection('sqlsrv')->table('trscaleb19s')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->join('products', 'products.itemCode', 'trscaleb19s.itemCode')->where('driver','like','%' . $this->katakunci . '%')->whereNotNull('netto')->orwhere('carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        } else {
            $data = DB::connection('sqlsrv')->table('trscaleb19s')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->join('products', 'products.itemCode', 'trscaleb19s.itemCode')->wherenotnull('timbangin')->wherenull('netto')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            // $sdhout = DB::connection('sqlsrv')->table('trscaleb19s')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->join('products', 'products.itemCode', 'trscaleb19s.itemCode')->whereNotNull('netto')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        }

        if ($this->katakunciout  !=null) {
            // $data = DB::connection('sqlsrv')->table('trscaleb19s')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->join('products', 'products.itemCode', 'trscaleb19s.itemCode')->where('driver','like','%' . $this->katakunci . '%')->wherenotnull('timbangin')->wherenull('netto')->orwhere('carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            $sdhout = DB::connection('sqlsrv')->table('trscaleb19s')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->join('products', 'products.itemCode', 'trscaleb19s.itemCode')->where('driver','like','%' . $this->katakunciout . '%')->whereNotNull('netto')->orwhere('carID','like','%' . $this->katakunciout . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        } else {
            // $data = DB::connection('sqlsrv')->table('trscaleb19s')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->join('products', 'products.itemCode', 'trscaleb19s.itemCode')->wherenotnull('timbangin')->wherenull('netto')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            $sdhout = DB::connection('sqlsrv')->table('trscaleb19s')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->join('products', 'products.itemCode', 'trscaleb19s.itemCode')->whereNotNull('netto')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        }

        $timinlist = DB::connection('sqlsrv')->table('trscaleb19s')->select('trscaleb19s.id','trscaleb19s.carID','suppliers.suppName','suppliers.suppID', 'trscaleb19s.timbangin', 'trscaleb19s.netto')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->wherenotnull('timbangin')->wherenull('netto')->get();
        $timbangan = JembatanTimbang::all();
        return view('livewire.timbangoutmaterial',['datascaleout' => $sdhout,'datatim' => $data, 'datareg1' => $timinlist, 'timbangan' => $timbangan]);
    }
}
