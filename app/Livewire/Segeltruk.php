<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Segeltruk extends Component
{

    
    use WithFileUploads;
    use WithPagination;
    public $katakunci;
    public $driver;
    public $carID;
    public $custName;
    public $itemName;
    public $itemType;
    #[Validate('required', message: 'Pilih Truk yang akan di segel')]
    public $transID;
    #[Validate('required', message: 'Seal Belum Diisi')]
    public $sealNo1;
    public $sealNo2;
    public $sealNo3;
    public $sealNo4;
    public $sealNo5;
    public $spmNo;
    public $jenisTruk;
    public $jenisTruk1 = 1;
    public $fototruk;
    public $scaleID;
    #[Rule('max:1024', message: 'Foto Seal1 maks 1 MB')]
    #[Validate('required', message: 'Silahkan Upload foto Seal1')]
    #[Validate('image', message: 'Seal1 harus image')]
    public $fotoSealNo1;
    // #[Validate('required', message: 'Silahkan Upload foto Seal2')]
    #[Rule('max:1024', message: 'Foto Seal2 maks 1 MB')]
    // #[Validate('image', message: 'Seal2 harus image')]
    public $fotoSealNo2;
    // #[Rule('max:1024', message: 'Foto Seal3 maks 1 MB')]
    #[Rule('max:1024', message: 'Foto Seal2 maks 1 MB')]
    // #[Validate('image', message: 'Seal3 harus image')]
    public $fotoSealNo3;
    // #[Rule('max:1024', message: 'Foto Seal4 maks 1 MB')]
    #[Rule('max:1024', message: 'Foto Seal2 maks 1 MB')]
    // #[Validate('image', message: 'Seal4 harus image')]
    public $fotoSealNo4;
    // #[Rule('max:1024', message: 'Foto Seal5 maks 1 MB')]
    // #[Validate('image', message: 'Seal5 harus image')]
    #[Rule('max:1024', message: 'Foto Seal2 maks 1 MB')]
    public $fotoSealNo5;

    public function store()
    {
        
        $tgl = Carbon::now();
        $userIDApp = Auth::user()->id;
        $usernameApp = Auth::user()->username;
        $replaceseal = str_replace("/","-",$this->spmNo);
        $fotoSealNo1 = $replaceseal . '-1.jpg';
        $fotoSealNo2 = $replaceseal . '-2.jpg';
        $fotoSealNo3 = $replaceseal . '-3.jpg';
        $fotoSealNo4 = $replaceseal . '-4.jpg';
        $fotoSealNo5 = $replaceseal . '-5.jpg';
        $this->validate();

        try {
            if ($this->itemType == 'FG-L') {
                DB::connection('sqlsrv')->table('createspms')->where('id',$this->transID)->update([
                    'sealNo1' => $this->sealNo1,
                    'sealNo2' => $this->sealNo2,
                    'sealNo3' => $this->sealNo3,
                    'sealNo4' => $this->sealNo4,
                    'sealNo5' => $this->sealNo5,
                    'fotoSealNo1' => 'uploads/segel/' . $fotoSealNo1,
                    'fotoSealNo2' => 'uploads/segel/' . $fotoSealNo2,
                    'fotoSealNo3' => 'uploads/segel/' . $fotoSealNo3,
                    'fotoSealNo4' => 'uploads/segel/' . $fotoSealNo4,
                    'fotoSealNo5' => 'uploads/segel/' . $fotoSealNo5,
                    
                ]);

                DB::connection('sqlsrv')->table('trscale')->where('id',$this->scaleID)->update([
                   
                    'isLoadingDone' => 1,
                    'isLoadingDoneDate' => $tgl,
                    
                ]);

            } elseif ($this->jenisTruk1 == 1)  {
                DB::connection('sqlsrv')->table('createspms')->where('id',$this->transID)->update([
                    'sealNo1' => $this->sealNo1,
                    'sealNo2' => $this->sealNo2,
                    'sealNo3' => $this->sealNo3,
                    'sealNo4' => $this->sealNo4,
                    'sealNo5' => $this->sealNo5,
                    'fotoSealNo1' => 'uploads/segel/' . $fotoSealNo1,
                    
                ]);
                // dd(1);
            } else   {
                DB::connection('sqlsrv')->table('createspms')->where('id',$this->transID)->update([
                    'sealNo1' => $this->sealNo1,
                    'sealNo2' => $this->sealNo2,
                    'sealNo3' => $this->sealNo3,
                    'sealNo4' => $this->sealNo4,
                    'sealNo5' => $this->sealNo5,
                    'fotoSealNo1' => 'uploads/segel/' . $fotoSealNo1,
                    'fotoSealNo2' => 'uploads/segel/' . $fotoSealNo2,
                    
                ]);
                // dd(2);
            }
            
            if($this->fotoSealNo1){
                $this->fotoSealNo1->storeAs('uploads/segel',$fotoSealNo1,'public');
            }

            if($this->fotoSealNo2){
                $this->fotoSealNo2->storeAs('uploads/segel',$fotoSealNo2,'public');
            }
            
            if($this->fotoSealNo3){
                $this->fotoSealNo3->storeAs('uploads/segel',$fotoSealNo3,'public');
            }

            if($this->fotoSealNo4){
                $this->fotoSealNo4->storeAs('uploads/segel',$fotoSealNo4,'public');
            }

            if($this->fotoSealNo5){
                $this->fotoSealNo5->storeAs('uploads/segel',$fotoSealNo5,'public');
            }

            session()->flash('message', 'Data berhasil dimasukkan');
            $this->clear();
            redirect('/segeltruk');
            

        } catch (\Throwable $th) {
            
            
            session()->flash('error', 'gagal menyimpan data');
            
        }
    }

    public function edit($id)
    {   
        $this->sealNo1 = '';
        $this->sealNo2 = '';
        $this->sealNo3 = '';
        $this->sealNo4 = '';
        $this->sealNo5 = '';
        $data = DB::connection('sqlsrv')->table('createspms')->join('customers', 'customers.custID', 'createspms.custID')->join('trscale', 'trscale.spmID', 'createspms.id')->join('products', 'products.itemCode', 'createspms.itemCode')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->select('trscale.id as scaleID','createspms.id as spm_ID','createspms.driver','createspms.spmNo','createspms.carID','customers.custName','products.itemName','products.type','createspms.spmJenisTruk','jenistruks.jenisTruk')->where('createspms.id', $id)->first();
        // dd($data);
        $this->driver = $data->driver;
        $this->scaleID = $data->scaleID;
        $this->carID = $data->carID;
        $this->custName = $data->custName;
        $this->itemName = $data->itemName;
        $this->itemType = $data->type;
        $this->spmNo = $data->spmNo;
        $this->transID = $data->spm_ID;
        $this->jenisTruk = $data->spmJenisTruk . ' - ' . $data->jenisTruk  ;
        $this->jenisTruk1 = $data->spmJenisTruk ;
        
        if ($this->jenisTruk1 == 1) {
            $this->fototruk = '/truk/1.png';
            
        } else if($this->jenisTruk1 == 2) {
            $this->fototruk = '/truk/2.png';
        } else if($this->jenisTruk1 == 3) {
            $this->fototruk = '/truk/3.png';
        } else if($this->jenisTruk1 == 4) {
            $this->fototruk = '/truk/4.png';
        } else if($this->jenisTruk1 == 5) {
            $this->fototruk = '/truk/5.png';
        } else if($this->jenisTruk1 == 6) {
            $this->fototruk = '/truk/6.png';
        } else if($this->jenisTruk1 == 7) {
            $this->fototruk = '/truk/7.png';
        } else {
            $this->fototruk = '/storage/uploads/noimage.jpg';
        }

 
    }

    public function clear()
    {
        redirect('/segeltruk');
    }

    public function render()
    {
        $datagula = DB::connection('sqlsrv')->table('createspms')->join('customers', 'customers.custID', 'createspms.custID')->join('trscale', 'trscale.spmID', 'createspms.id')->join('products', 'products.itemCode', 'createspms.itemCode')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->whereNotNull('jam_out')->where('type','<>','FG-L')->whereNull('sealNo1')->select('createspms.id as id','createspms.driver','createspms.carID','products.itemName','createspms.spmNo','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type' )->orderBy('createspms.id','desc')->paginate(5);
        $datamol = DB::connection('sqlsrv')->table('createspms')->join('customers', 'customers.custID', 'createspms.custID')->join('trscale', 'trscale.spmID', 'createspms.id')->join('products', 'products.itemCode', 'createspms.itemCode')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->where('type','=','FG-L')->whereNotNull('isLoading')->whereNull('isLoadingDone')->whereNull('sealNo1')->select('createspms.id as id','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type' )->orderBy('createspms.id','desc')->paginate(5);
        if ($this->katakunci !=null) {
            $donesegel = DB::connection('sqlsrv')->table('createspms')->join('customers', 'customers.custID', 'createspms.custID')->join('trscale', 'trscale.spmID', 'createspms.id')->join('products', 'products.itemCode', 'createspms.itemCode')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->whereNotNull('isLoadingDone')->wherenotNull('sealNo1')->where('createspms.carID','like','%' . $this->katakunci . '%')->select('createspms.id as id','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type' )->orderBy('createspms.id','desc')->paginate(5);
        } else {
            $donesegel = DB::connection('sqlsrv')->table('createspms')->join('customers', 'customers.custID', 'createspms.custID')->join('trscale', 'trscale.spmID', 'createspms.id')->join('products', 'products.itemCode', 'createspms.itemCode')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->whereNotNull('isLoadingDone')->wherenotNull('sealNo1')->select('createspms.id as id','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type' )->orderBy('createspms.id','desc')->paginate(5);
        }
        // $datamol = DB::connection('sqlsrv')->table('createspms')->join('customers', 'customers.custID', 'createspms.custID')->join('trscale', 'trscale.spmID', 'createspms.id')->join('products', 'products.itemCode', 'createspms.itemCode')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->where('itemName','like','%mola%')->whereNotNull('isLoading')->whereNull('sealNo1')->select('createspms.id as id','createspms.driver','createspms.carID','products.itemName','customers.custName','jenistruks.jenisTruk' )->orderBy('createspms.id','desc')->paginate(5);
        
        // dd($donesegel);
        return view('livewire.segeltruk',['trukgula' => $datagula, 'trukmol' => $datamol, 'truksdhsegel' => $donesegel]);
    }
}
