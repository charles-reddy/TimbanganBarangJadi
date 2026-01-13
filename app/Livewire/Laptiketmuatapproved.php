<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Laptiketmuatapproved extends Component
{
    use WithPagination;
    public $tiketMuat;
    public $platNo;
    public $driver;
    public $simKtp;
    public $stnk;
    public $katakunci;
    public $altImg;
    public $transID;
    public $katacust;
    public $tglMuat;
    public $ip;


    public function store()
    {
        

        try {
               
            DB::connection('sqlsrv')->table('create_t_m_s')->where('id',$this->transID)->update([
                'isMktApp' => null,
                
                
            ]);
            
            session()->flash('message', 'Approval berhasil Dibatalkan');
            $this->clear();
            redirect('/laptiketmuatapproved');
            

        } catch (\Throwable $th) {
            
            
            session()->flash('error', 'gagal menyimpan data');
            
        }
    }


    public function edit($id)
    {
        $this->ip = substr(request()->ip(),0,2);
        $data = DB::connection('sqlsrv')->table('create_t_m_s')->where('id', $id)->first();
        // dd($data->simKtp);
        $this->tiketMuat = $data->pendfNo;
        $this->transID = $id;
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
        
       
    }

    public function cancel($id)
    {
        $data = DB::connection('sqlsrv')->table('create_t_m_s')->where('id', $id)->first();
        // dd($data->simKtp);
        $this->tiketMuat = $data->pendfNo;
        $this->transID = $id;
       
       
    }

     public function clear()
    {
        redirect('/laptiketmuatapproved');
    }

    public function render()
    {
        if ($this->katakunci  !=null) {
            $data = DB::connection('sqlsrv')->table('create_t_m_s')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->join('customers', 'customers.custID', 'create_t_m_s.custID')->select('create_t_m_s.id','pendfNo', 'tmQtyKg','tmQtyKarung','sppbNo','tmCarID','isMktApp','tglMuat','custName','isSecCek','tmTranspName', 'isSecCekDate')->where('isMktApp','1')->where('pendfNo','like','%' . $this->katakunci . '%')->orderBy('id','desc')->paginate(10);
        } elseif ($this->katacust  !=null) {
            $data = DB::connection('sqlsrv')->table('create_t_m_s')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->join('customers', 'customers.custID', 'create_t_m_s.custID')->select('create_t_m_s.id','pendfNo', 'tmQtyKg','tmQtyKarung','sppbNo','tmCarID','isMktApp','tglMuat','custName','isSecCek','tmTranspName', 'isSecCekDate')->where('isMktApp','1')->where('custName','like','%' . $this->katacust . '%')->orderBy('id','desc')->paginate(10);
        } elseif ($this->tglMuat  !=null) {
            $data = DB::connection('sqlsrv')->table('create_t_m_s')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->join('customers', 'customers.custID', 'create_t_m_s.custID')->select('create_t_m_s.id','pendfNo', 'tmQtyKg','tmQtyKarung','sppbNo','tmCarID','isMktApp','tglMuat','custName','isSecCek','tmTranspName', 'isSecCekDate')->where('isMktApp','1')->wheredate('tglMuat','=', $this->tglMuat)->orderBy('id','desc')->paginate(10);
        } else {
            $data = DB::connection('sqlsrv')->table('create_t_m_s')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('createspms', 'createspms.tiketID', 'create_t_m_s.id')->join('trscale', 'trscale.spmID', 'createspms.id')->select('create_t_m_s.id','pendfNo', 'tmQtyKg','tmQtyKarung','createsppbs.sppbNo','tmCarID','isMktApp','tglMuat','custName','isSecCek','tmTranspName', 'isSecCekDate', 'jam_out')->where('isMktApp','1')->orderBy('id','desc')->paginate(10);
        // dd($data);
        }
        return view('livewire.laptiketmuatapproved',['datatiketmuat' => $data]);
    }
}
