<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Approvaltiketmuat extends Component
{
    use WithPagination;
    public $katakunci;
    #[Validate('required', message: 'Pilih Tiket Muat dari Customer')]
    public $transID;
    public $sppbNo;
    public $tglMuat;
    public $custName;
    public $isApp;
    public $qtyKg;
    public $qtyKarung;
    public $itemName;
    public $simKtp;
    public $stnk;
    public $ip;


    public function store()
    {
        $tgl = Carbon::now();
        $userIDApp = Auth::user()->id;
        $usernameApp = Auth::user()->username;
        $this->validate();

        try {
               
            DB::connection('sqlsrv')->table('create_t_m_s')->where('id',$this->transID)->update([
                'isMktApp' => 1,
                'isMktAppID' => $userIDApp,
                'isAppDate' => $tgl,
                
            ]);
            
            session()->flash('message', 'Data berhasil dimasukkan');
            $this->clear();
            redirect('/approvaltiketmuat');
            

        } catch (\Throwable $th) {
            
            
            session()->flash('error', 'gagal menyimpan data');
            
        }
    }

    public function clear()
    {
        redirect('/approvaltiketmuat');
    }

    public function edit($id)
    {
        $this->ip = substr(request()->ip(),0,2);
        $data = DB::connection('sqlsrv')->table('create_t_m_s')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->select('create_t_m_s.id','create_t_m_s.tmSppbID','create_t_m_s.pendfNo','create_t_m_s.tmCarID','create_t_m_s.tmDriver','create_t_m_s.noHPDriver','create_t_m_s.tmTranspName','create_t_m_s.tmQtyKarung','create_t_m_s.tmQtyKg','create_t_m_s.tglMuat','products.itemName','customers.custName','createsppbs.sppbNo','create_t_m_s.simKtp','create_t_m_s.stnk')->where('create_t_m_s.id', $id)->first();
        
        $this->sppbNo = $data->sppbNo;
        $this->transID = $id;
        $this->custName = $data->custName;
        $this->tglMuat = $data->tglMuat;
        $this->qtyKg = number_format($data->tmQtyKg);
        $this->qtyKarung = number_format($data->tmQtyKarung);
        $this->itemName = $data->itemName;

        if ($data->simKtp != null) {
            if($this->ip == '10.20.3.9') {
                // dd('local');
                $this->simKtp = 'http://10.20.1.64:8104/storage/' . $data->simKtp;
            } else {
                // dd('outside');
                $this->simKtp = 'https://customer.appktm.com/storage/' . $data->simKtp;
            }
        } else {
            if($this->ip == '10.20.3.9') {
                // dd('local');
                $this->simKtp = 'http://10.20.1.64:8104/storage/uploads/noimage.jpg';
            } else {
                // dd('outside');
                $this->simKtp = 'https://customer.appktm.com/storage/uploads/noimage.jpg';
            }
            // $this->simKtp = '/storage/uploads/noimage.jpg';
        }

        if ($data->stnk != null) {
            if($this->ip == '10.20.3.9') {
                // dd('local');
                $this->stnk = 'http://10.20.1.64:8104/storage/' . $data->stnk;
            } else {
                $this->stnk = 'https://customer.appktm.com/storage/' . $data->stnk;
            }
            
        } else {
            if($this->ip == '10.20.3.9') {
                // dd('local');
                $this->stnk = 'http://10.20.1.64:8104/storage/uploads/noimage.jpg';
            } else {
                // dd('outside');
                $this->stnk = 'https://customer.appktm.com/storage/uploads/noimage.jpg';
            }
        }
        // dd($data->stnk, $data->simKtp);
    }


    public function render()
    {
        if ($this->katakunci  !=null) {
            $data = DB::connection('sqlsrv')->table('create_t_m_s')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->select('create_t_m_s.id','create_t_m_s.tmSppbID','create_t_m_s.pendfNo','create_t_m_s.tmCarID','create_t_m_s.tmDriver','create_t_m_s.noHPDriver','create_t_m_s.tmTranspName','create_t_m_s.tmQtyKarung','create_t_m_s.tmQtyKg','create_t_m_s.tglMuat','products.itemName','customers.custName','createsppbs.sppbNo')->whereNull('ismktapp')->where('create_t_m_s.tmQtyKg','>', 0)->where('createsppbs.sppbNo','like','%' . $this->katakunci . '%')->orderBy('id','desc')->paginate(10);
        
        } else {
            $data = DB::connection('sqlsrv')->table('create_t_m_s')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->select('create_t_m_s.id','create_t_m_s.tmSppbID','create_t_m_s.pendfNo','create_t_m_s.tmCarID','create_t_m_s.tmDriver','create_t_m_s.noHPDriver','create_t_m_s.tmTranspName','create_t_m_s.tmQtyKarung','create_t_m_s.tmQtyKg','create_t_m_s.tglMuat','products.itemName','customers.custName','createsppbs.sppbNo')->whereNull('ismktapp')->where('create_t_m_s.tmQtyKg','>', 0)->orderBy('id','desc')->paginate(10);
            
        }
        // dd($data);
        return view('livewire.approvaltiketmuat', ['datatiketmuat' => $data]);
    }
}
