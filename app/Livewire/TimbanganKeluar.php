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
use PhpParser\Node\Stmt\Switch_;
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
    public $itemType;
    public $sppbID;
    public $tmQtyKg;
    public $hasilOpenQtyKg;
    public $openQtyKg;
    
    
    
    public function timbang()
    {
        $this->timbangout = '';
        $this->netto = '';
        try {

            // *************** testing timbangan *******************
        $iptimbangan = JembatanTimbang::where('timbanganID', '=',$this->timbanganoutID)->value('IP');
        $this->timbangin;
        $this->timbangout = 80555;
        $this->netto = $this->timbangin - $this->timbangout; 
        if ($this->netto < 0)
        {
           
            $this->netto = $this->timbangout - $this->timbangin; 
        } 
        // *************** testing timbangan *******************  
            
            // switch ($this->timbanganoutID) {
            //     case 1:
            //         $data = "http://10.20.1.49:3000/api/weight/SCALE_10";
            //         break;
                
            //     case '2':
            //         $data = "http://10.20.1.49:3000/api/weight/SCALE_09";
            //         break;   

            //     case '3':
            //         $data = "http://10.20.1.49:3000/api/weight/SCALE_08";
            //         break; 

            //     default:
                    
            //         break;
            // }

            //     $client= new Client();
            //     // $data = "http://10.20.1.49:3000/api/weight/SCALE_09";
            //     $response = $client->request('GET',$data);
            //     $content =  $response->getBody()->getContents();
            //     $contentarray = json_decode($content,true);
            // //    dd($contentarray['weight']);
            //      $this->timbangout = $contentarray['weight'];
            //      $this->timbangin;
        
            // $this->netto = $this->timbangin - $this->timbangout; 
            // if ($this->netto < 0)
            // {
            
            //     $this->netto = $this->timbangout - $this->timbangin; 
            // } 

        } catch (Exception $e) {
            session()->flash('error', 'Pastikan Timbangan yg dipilih sesuai');
            return;
        }

        if ($this->itemCode == 'S8A000390D' or $this->itemCode == 'S8B000390D' ) 
        {
            //  dd($this->itemCode);
            $this->avgKarung = 50.11;
        }  else {
            $this->avgKarung = $this->netto / $this->b10QtyKarung;
            
        }
        
       $this->hasilOpenQtyKg = $this->openQtyKg + ($this->tmQtyKg - $this->netto);
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
        // dd($data->spmID);
        $dataspm = DB::connection('sqlsrv')->table('createsppbs')->join('createspms','createspms.sppbNo','createsppbs.id')->join('create_t_m_s','create_t_m_s.tmSppbID','createsppbs.id')->where('createspms.id',$data->spmID)->select('createsppbs.id','createsppbs.openQtyKg','create_t_m_s.tmQtyKg')->first();
        // dd($dataspm);
        $this->sppbID = $dataspm->id;
        $this->tmQtyKg = $dataspm->tmQtyKg;
        $this->openQtyKg = $dataspm->openQtyKg;
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
        $itemT = Product::where('itemCode', $this->itemCode)->value('type');
        $this->itemName = $itemC;
        $this->itemType = $itemT;
        $this->updateData = true;
        $this->id_trscale = $id;
        $this->b10QtyKarung = $data->b10QtyKarung;
 
    }

    public function update()
    {
     
        $tgl = Carbon::now();
        
        $userIDOUT = Auth::user()->id;
        $usernameOUT = Auth::user()->username;
        // dd($this->isApp);

        switch ($this->itemType) {
                case 'FG-L':

                    // dd('liquid');
                    try {
                        DB::connection('sqlsrv')->table('trscale')->where('id',$this->id_trscale)->update([
                            'avgKarung' => 50.03,
                            
                            ]);
                            
                            $this->jam_out = Carbon::now();
                            $this->userIDOUT = $userIDOUT; 
                            $this->usernameOUT = $usernameOUT;
                            $rules = [
                                'driver' => 'required',
                                'carID' => 'required',
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
                               
                                
                                
                            ];
                            $validated = $this->validate($rules, $pesan); 
                            $data = Trscale::find($this->id_trscale); 
                            $id=$this->id_trscale;
                            $combineid = '/cetakout/'. $id ;
                            $data->update($validated);
                            
                            DB::connection('sqlsrv')->table('createsppbs')->where('id',$this->sppbID)->update([
                            'openQtyKg' => $this->hasilOpenQtyKg,
                            
                            ]);

                            // dd($combineid);
                            session()->flash('message', 'Data berhasil diperbaharui');
                            redirect($combineid);
                           
                    } catch (Exception $e) {
                        throw $e;
                        return;
                    }
                    break;
                
                

                default:
                try {

                    switch ($this->itemCode) {
                        ########   kemasan 1 kg plastik awal ####
                        case 'S2IBE1390D':
                            // dd('1 kg plastik');
                            if ($this->avgKarung >= 20.239 and $this->avgKarung < 20.522) {
                                //    dd('1');         
                                DB::connection('sqlsrv')->table('trscale')->where('id',$this->id_trscale)->update([
                                'avgKarung' => $this->avgKarung,
                                
                                ]);
                                
                                $this->jam_out = Carbon::now();
                                $this->userIDOUT = $userIDOUT; 
                                $this->usernameOUT = $usernameOUT;
                                $rules = [
                                    'driver' => 'required',
                                    'carID' => 'required',
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
                                    // 'carID.max' => 'carid data max 10 digit',
                                    
                                    
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
                            
                            } else if ( ($this->isApp)  == 1 and  ($this->avgKarung >= 20.522)   ) {
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
                                            'carID' => 'required',
                                        
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
                                            // 'carID.max' => 'carid data max 10 digit',
                                            
                                            
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
                                    } else if ( (($this->isApp)  == 1) and ($this->avgKarung < 20.239)   ) {
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
                                                'carID' => 'required',
                                            
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
                                                // 'carID.max' => 'carid data max 10 digit',
                                                
                                                
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
                                        
                                        DB::connection('sqlsrv')->table('logAppAvgKarung')->insert([
                                    
                                            'timID' => $userIDOUT,
                                            'trscaleID' => $this->id_trscale,
                                            'avgKarung' => $this->avgKarung,
                                            'timDate' => $tgl,
                                            
                                        ]);

                                        session()->flash('error', 'Avg Karung tidak sesuai range');
                                    
                                        return;
                
                            }
                            break;
                        ########   kemasan 1 kg plastik akhir ####

                        ########   kemasan 1 kg karton awal #### 
                        case 'S2IBEV390D':
                            // dd('1 kg karton');
                            if ($this->avgKarung >= 20.621 and $this->avgKarung < 20.773) {
                                //    dd('1');         
                                DB::connection('sqlsrv')->table('trscale')->where('id',$this->id_trscale)->update([
                                'avgKarung' => $this->avgKarung,
                                
                                ]);
                                
                                $this->jam_out = Carbon::now();
                                $this->userIDOUT = $userIDOUT; 
                                $this->usernameOUT = $usernameOUT;
                                $rules = [
                                    'driver' => 'required',
                                    'carID' => 'required',
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
                                    // 'carID.max' => 'carid data max 10 digit',
                                    
                                    
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
                            
                            } else if ( ($this->isApp)  == 1 and  ($this->avgKarung >= 20.773)   ) {
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
                                            'carID' => 'required',
                                        
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
                                            // 'carID.max' => 'carid data max 10 digit',
                                            
                                            
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
                                    } else if ( (($this->isApp)  == 1) and ($this->avgKarung < 20.621)   ) {
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
                                                'carID' => 'required',
                                            
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
                                                // 'carID.max' => 'carid data max 10 digit',
                                                
                                                
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
                                        
                                        DB::connection('sqlsrv')->table('logAppAvgKarung')->insert([
                                    
                                            'timID' => $userIDOUT,
                                            'trscaleID' => $this->id_trscale,
                                            'avgKarung' => $this->avgKarung,
                                            'timDate' => $tgl,
                                            
                                        ]);

                                        session()->flash('error', 'Avg Karung tidak sesuai range');
                                    
                                        return;
                
                            }
                            break;
                        #######   kemasan 1 kg karton akhir #### 


                        
                        ########   kemasan 500 gram plastik awal #### 
                        case 'S2IBF1390D':
                            // dd('500 gram plastik');
                            if ($this->avgKarung >= 10.159 and $this->avgKarung < 10.271) {
                                //    dd('1');         
                                DB::connection('sqlsrv')->table('trscale')->where('id',$this->id_trscale)->update([
                                'avgKarung' => $this->avgKarung,
                                
                                ]);
                                
                                $this->jam_out = Carbon::now();
                                $this->userIDOUT = $userIDOUT; 
                                $this->usernameOUT = $usernameOUT;
                                $rules = [
                                    'driver' => 'required',
                                    'carID' => 'required',
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
                                    // 'carID.max' => 'carid data max 10 digit',
                                    
                                    
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
                            
                            } else if ( ($this->isApp)  == 1 and  ($this->avgKarung >= 10.271)   ) {
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
                                            'carID' => 'required',
                                        
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
                                            // 'carID.max' => 'carid data max 10 digit',
                                            
                                            
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
                                    } else if ( (($this->isApp)  == 1) and ($this->avgKarung < 10.159)   ) {
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
                                                'carID' => 'required',
                                            
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
                                                // 'carID.max' => 'carid data max 10 digit',
                                                
                                                
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
                                        
                                        DB::connection('sqlsrv')->table('logAppAvgKarung')->insert([
                                    
                                            'timID' => $userIDOUT,
                                            'trscaleID' => $this->id_trscale,
                                            'avgKarung' => $this->avgKarung,
                                            'timDate' => $tgl,
                                            
                                        ]);

                                        session()->flash('error', 'Avg Karung tidak sesuai range');
                                    
                                        return;
                
                            }
                            break;
                        #######   kemasan 500 gram palstik akhir #### 
                            
                        
                        ########   kemasan 500 gram karton awal #### 
                        case 'S2IBF1390D':
                            //  dd('500 gram karton');
                            if ($this->avgKarung >= 10.414 and $this->avgKarung < 10.531) {
                                //    dd('1');         
                                DB::connection('sqlsrv')->table('trscale')->where('id',$this->id_trscale)->update([
                                'avgKarung' => $this->avgKarung,
                                
                                ]);
                                
                                $this->jam_out = Carbon::now();
                                $this->userIDOUT = $userIDOUT; 
                                $this->usernameOUT = $usernameOUT;
                                $rules = [
                                    'driver' => 'required',
                                    'carID' => 'required',
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
                                    // 'carID.max' => 'carid data max 10 digit',
                                    
                                    
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
                            
                            } else if ( ($this->isApp)  == 1 and  ($this->avgKarung >= 10.531)   ) {
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
                                            'carID' => 'required',
                                        
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
                                            // 'carID.max' => 'carid data max 10 digit',
                                            
                                            
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
                                    } else if ( (($this->isApp)  == 1) and ($this->avgKarung < 10.414)   ) {
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
                                                'carID' => 'required',
                                            
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
                                                // 'carID.max' => 'carid data max 10 digit',
                                                
                                                
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
                                        
                                        DB::connection('sqlsrv')->table('logAppAvgKarung')->insert([
                                    
                                            'timID' => $userIDOUT,
                                            'trscaleID' => $this->id_trscale,
                                            'avgKarung' => $this->avgKarung,
                                            'timDate' => $tgl,
                                            
                                        ]);

                                        session()->flash('error', 'Avg Karung tidak sesuai range');
                                    
                                        return;
                
                            }
                            break;
                        #######   kemasan 500 gram karton akhir #### 

                        #######   kemasan 50 Kg #### 
                            default:
                                if ($this->avgKarung >= 50.145 and $this->avgKarung < 50.203) {
                                        //    dd('1');         
                                    DB::connection('sqlsrv')->table('trscale')->where('id',$this->id_trscale)->update([
                                        'avgKarung' => $this->avgKarung,
                                        
                                        ]);
                                        
                                        $this->jam_out = Carbon::now();
                                        $this->userIDOUT = $userIDOUT; 
                                        $this->usernameOUT = $usernameOUT;
                                        $rules = [
                                            'driver' => 'required',
                                            'carID' => 'required',
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
                                            // 'carID.max' => 'carid data max 10 digit',
                                            
                                            
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
                                    
                                    } else if ( ($this->isApp)  == 1 and  ($this->avgKarung >= 50.203)   ) {
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
                                                    'carID' => 'required',
                                                
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
                                                    // 'carID.max' => 'carid data max 10 digit',
                                                    
                                                    
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
                                            } else if ( (($this->isApp)  == 1) and ($this->avgKarung < 50.145)   ) {
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
                                                        'carID' => 'required',
                                                    
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
                                                        // 'carID.max' => 'carid data max 10 digit',
                                                        
                                                        
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
                                                
                                                DB::connection('sqlsrv')->table('logAppAvgKarung')->insert([
                                            
                                                    'timID' => $userIDOUT,
                                                    'trscaleID' => $this->id_trscale,
                                                    'avgKarung' => $this->avgKarung,
                                                    'timDate' => $tgl,
                                                    
                                                ]);

                                                session()->flash('error', 'Avg Karung tidak sesuai range');
                                            
                                                return;
                        
                                    }
                        
                            break;
                        #######   kemasan 50 Kg #### 
                    }
                    

                    
                    
                    
                    
                    
                } catch (Exception $e) {
                    
                    // session()->flash('error', 'failed to update data');
                    throw $e;
                    return;
                }
                    break;
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
        if ($this->katakunci   !=null) {
            $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunci . '%')->whereNull('netto')->whereNotNull('isLoadingDone')->orwhere('carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            // $sdhout = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunciout . '%')->whereNotNull('netto')->orwhere('carID','like','%' . $this->katakunciout . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        } else {
            $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->whereDate('jam_in','>', Carbon::now()->addDays(-5) )->wherenull('netto')->whereNotNull('isLoadingDone')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            // $sdhout = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->whereNotNull('netto')->whereNotNull('b10QtyKarung')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            //  dd($data);
        }

        if ($this->katakunciout  !=null) {
            // $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunci . '%')->whereNull('netto')->whereNotNull('b10QtyKarung')->orwhere('carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            $sdhout = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunciout . '%')->whereNotNull('netto')->orwhere('carID','like','%' . $this->katakunciout . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        } else {
            // $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->wherenull('netto')->whereNotNull('b10QtyKarung')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            $sdhout = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->whereDate('jam_out','>', Carbon::now()->addDays(-5) )->whereNotNull('netto')->whereNotNull('b10QtyKarung')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            //  dd($data);
        }


        $timbangan = JembatanTimbang::all();
        $pelanggan = Customer::all();
        $angkutan = Transporter::all();
        $barang = Product::where('itemName','like','%gkr%')->orwhere('itemName','like','%mola%')->get();
        return view('livewire.timbangan-keluar', ['datascaleout' => $sdhout,'datascale' => $data, 'customer' => $pelanggan, 'transporter' => $angkutan, 'product' => $barang, 'timbangan' => $timbangan]); 
    }
}
