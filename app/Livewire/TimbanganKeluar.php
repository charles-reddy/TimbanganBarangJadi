<?php

namespace App\Livewire;

use Livewire\Component;

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

use Livewire\WithPagination;
use PhpParser\Node\Stmt\TryCatch;

class TimbanganKeluar extends Component
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
    public $katakunciout;
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
    public $timbangout;
    public $transID;
    public $netto;
    public $timbanganoutID;
    public $custN;
    public $jam_out;
    public $userIDOUT;
    public $usernameOUT;
    public $avgKarung;
    public $b10QtyKarung;
    public $isApp;
    
    
    
    
    public function timbang()
    {
        $this->timbangout = '';
        $this->netto = '';
        // try {
        //     switch ($this->timbanganoutID) {
        //         case 1:
        //             $data = "http://10.20.1.49:3000/api/weight/SCALE_10";
        //             break;
                
        //         case '2':
        //             $data = "http://10.20.1.49:3000/api/weight/SCALE_09";
        //             break;   

        //         case '3':
        //             $data = "http://10.20.1.49:3000/api/weight/SCALE_08";
        //             break; 

        //         default:
                    
        //             break;
        //     }

        //         $client= new Client();
        //         // $data = "http://10.20.1.49:3000/api/weight/SCALE_09";
        //         $response = $client->request('GET',$data);
        //         $content =  $response->getBody()->getContents();
        //         $contentarray = json_decode($content,true);
        //     //    dd($contentarray['weight']);
        //          $this->timbangout = $contentarray['weight'];
        //          $this->timbangin;
        
        //     $this->netto = $this->timbangin - $this->timbangout; 
        //     if ($this->netto < 0)
        //     {
            
        //         $this->netto = $this->timbangout - $this->timbangin; 
        //     } 

        // } catch (Exception $e) {
        //     session()->flash('error', 'Pastikan Timbangan yg dipilih sesuai');
        //     return;
        // }
        
        $iptimbangan = JembatanTimbang::where('timbanganID', '=',$this->timbanganoutID)->value('IP');
        $this->timbangin;
        $this->timbangout = 8888;
        $this->netto = $this->timbangin - $this->timbangout; 
        if ($this->netto < 0)
        {
           
            $this->netto = $this->timbangout - $this->timbangin; 
        } 

        $this->avgKarung = $this->netto / $this->b10QtyKarung;
       
    }
    

    // public function store()
    // {
    //     try {
            
            
    //             $rules = [
    //                 'driver' => 'required',
    //                 'carID' => 'required',
    //                 'custID' => 'required',
    //                 'transpID' => 'required',
    //                 'itemCode' => 'required',
    //                 'doNo' => 'nullable',
    //                 'poNo' => 'nullable',
    //                 'remarks' => 'nullable',
    //                 'timbangin' => 'required',
    //                 'timbanganID' => 'required',
                    
    //             ];
            
    //             $pesan = [
    //                 'driver.required' => 'driver wajib diisi',
    //                 'carID.required' => 'car id wajib diisi',
    //                 'custID.required' => 'customer ID wajib diisi',
    //                 'transpID.required' => 'transporter ID wajib diisi',
    //                 'itemCode.required' => 'item Code wajib diisi',
    //                 'timbangin.required' => 'Data timbang kosong',
    //                 'timbanganID.required' => 'Pilih ID Timbangan',
    //             ];
                
    //             $validated = $this->validate($rules, $pesan);
    //             // dd($validated);
    //             Trscale::create($validated);
    //             session()->flash('message', 'Data berhasil dimasukkan');
    //             $this->clear();
                

    //     } catch (Exception $e) {
            
    //         session()->flash('error', 'failed to store data');
    //         return;
    //     }

    // }


    public function edit($id)
    {   
        $this->netto = '';
        $this->timbangout = '';
        $data = Trscale::find($id);
        $this->driver = $data->driver;
        $this->carID = $data->carID;
        $this->custID = $data->custID;
        $this->doNo = $data->doNo;
        $this->poNo = $data->poNo;
        $this->isApp = $data->isApp;
        $this->remarks = $data->remarks;
        $this->timbangin = $data->timbangin;
        $this->transID = $id;
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
        $this->b10QtyKarung = $data->b10QtyKarung;
 
    }

    public function update()
    {
        $userIDOUT = Auth::user()->id;
        $usernameOUT = Auth::user()->username;
        // dd($this->isApp);
        try {
            if ($this->avgKarung >= 50.01 and $this->avgKarung < 50.25) {
            //    dd('1');         
               DB::connection('sqlsrv')->table('trscale')->where('id',$this->id_trscale)->update([
                'avgKarung' => $this->avgKarung,
                
                ]);
                
                $this->jam_out = Carbon::now();
                $this->userIDOUT = $userIDOUT; 
                $this->usernameOUT = $usernameOUT;
                $rules = [
                    'driver' => 'required',
                    'carID' => 'required|max:10',
                    'doNo' => 'nullable',
                    'poNo' => 'nullable',
                    'timbangout' => 'required',
                    'netto' => 'required',
                    'timbanganoutID' => 'required',
                    'remarks' => 'nullable',
                    'jam_out' => 'required',
                    'userIDOUT' => 'required',
                    'usernameOUT' => 'required',
                ];
                $pesan = [
                    
                    'timbangout.required' => 'data timbang out kosong',
                    'netto.required' => 'data netto kosong',
                    'timbanganoutID.required' => 'ID timbangan kosong',
                    'driver.required' => 'driver wajib diisi',
                    'carID.required' => 'car id wajib diisi',
                    'carID.max' => 'carid data max 10 digit',
                    
                    
                ];
                $validated = $this->validate($rules, $pesan);
                $data = Trscale::find($this->id_trscale);
                $id=$this->id_trscale;
                $combineid = '/cetakout/'. $id ;
                $data->update($validated);
                // dd($combineid);
                session()->flash('message', 'Data berhasil diperbaharui');
                redirect($combineid);
                // $this->clear();
            
            } else if ( ($this->isApp)  == 1 and  ($this->avgKarung >= 50.25)   ) {
                    // dd($this->timbangout, $this->netto, $this->timbanganoutID);
                    //  dd('2');
                    DB::connection('sqlsrv')->table('trscale')->where('id',$this->id_trscale)->update([
                        'avgKarung' => $this->avgKarung,
                        
                    ]);
                        $this->jam_out = Carbon::now();
                        $this->userIDOUT = $userIDOUT; 
                        $this->usernameOUT = $usernameOUT;
                        $rules = [
                            'driver' => 'required',
                            'carID' => 'required|max:10',
                           
                            'doNo' => 'nullable',
                            'poNo' => 'nullable',
                            
                            'timbangout' => 'required',
                            'netto' => 'required',
                            'timbanganoutID' => 'required',
                            'remarks' => 'nullable',
                            'jam_out' => 'required',
                            'userIDOUT' => 'required',
                            'usernameOUT' => 'required',
                        ];
                        $pesan = [
                            
                            'timbangout.required' => 'data timbang out kosong',
                            'netto.required' => 'data netto kosong',
                            'timbanganoutID.required' => 'ID timbangan kosong',
                            'driver.required' => 'driver wajib diisi',
                            'carID.required' => 'car id wajib diisi',
                            'carID.max' => 'carid data max 10 digit',
                            
                            
                        ];
                        $validated = $this->validate($rules, $pesan);
                     
                        $data = Trscale::find($this->id_trscale);
                        $id=$this->id_trscale;
                        $combineid = '/cetakout/'. $id ;
                        $data->update($validated);
                        // dd($combineid);
                        session()->flash('message', 'Data berhasil diperbaharui');
                        redirect($combineid);
                        // $this->clear();
            
                        return;
                    } else if ( (($this->isApp)  == 1) and ($this->avgKarung < 50.01)   ) {
                        // dd($this->timbangout, $this->netto, $this->timbanganoutID);
                        //  dd('3');
                        DB::connection('sqlsrv')->table('trscale')->where('id',$this->id_trscale)->update([
                            'avgKarung' => $this->avgKarung,
                            
                        ]);
                            $this->jam_out = Carbon::now();
                            $this->userIDOUT = $userIDOUT; 
                            $this->usernameOUT = $usernameOUT;
                            $rules = [
                                'driver' => 'required',
                                'carID' => 'required|max:10',
                               
                                'doNo' => 'nullable',
                                'poNo' => 'nullable',
                                
                                'timbangout' => 'required',
                                'netto' => 'required',
                                'timbanganoutID' => 'required',
                                'remarks' => 'nullable',
                                'jam_out' => 'required',
                                'userIDOUT' => 'required',
                                'usernameOUT' => 'required',
                            ];
                            $pesan = [
                                
                                'timbangout.required' => 'data timbang out kosong',
                                'netto.required' => 'data netto kosong',
                                'timbanganoutID.required' => 'ID timbangan kosong',
                                'driver.required' => 'driver wajib diisi',
                                'carID.required' => 'car id wajib diisi',
                                'carID.max' => 'carid data max 10 digit',
                                
                                
                            ];
                            $validated = $this->validate($rules, $pesan);
                         
                            $data = Trscale::find($this->id_trscale);
                            $id=$this->id_trscale;
                            $combineid = '/cetakout/'. $id ;
                            $data->update($validated);
                            // dd($combineid);
                            session()->flash('message', 'Data berhasil diperbaharui');
                            redirect($combineid);
                            // $this->clear();
                
                            return;
                } else {
                
                // dd('4');
                DB::connection('sqlsrv')->table('trscale')->where('id',$this->id_trscale)->update([
                    'avgKarung' => $this->avgKarung,
                    
                ]);
                session()->flash('error', 'Avg Karung tidak sesuai range');
            
                return;

            }

            
            
            
            
        } catch (Exception $e) {
            
            // session()->flash('error', 'failed to update data');
            throw $e;
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
        $this->timbangout = '';
        
       
        
        redirect('/timkeluar');
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
        if (($this->katakunci or $this->katakunciout)  !=null) {
            $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunci . '%')->whereNull('netto')->orwhere('carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            $sdhout = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunciout . '%')->whereNotNull('netto')->orwhere('carID','like','%' . $this->katakunciout . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        } else {
            $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->wherenull('netto')->whereNotNull('b10QtyKarung')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            $sdhout = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->whereNotNull('netto')->whereNotNull('b10QtyKarung')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            // dd($sdhout);
        }
        $timbangan = JembatanTimbang::all();
        $pelanggan = Customer::all();
        $angkutan = Transporter::all();
        $barang = Product::all();
        return view('livewire.timbangan-keluar', ['datascaleout' => $sdhout,'datascale' => $data, 'customer' => $pelanggan, 'transporter' => $angkutan, 'product' => $barang, 'timbangan' => $timbangan]); 
    }
}
