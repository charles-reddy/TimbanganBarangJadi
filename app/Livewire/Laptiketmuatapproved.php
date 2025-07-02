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
        $data = DB::connection('sqlsrv')->table('create_t_m_s')->where('id', $id)->first();
        // dd($data->simKtp);
        $this->tiketMuat = $data->pendfNo;
        $this->transID = $id;
        if ($data->simKtp != null) {
            $this->simKtp = '/storage/' . $data->simKtp;
            
        } else {
            $this->simKtp = '/storage/uploads/noimage.jpg';
        }

        if ($data->stnk != null) {
            $this->stnk = '/storage/' . $data->stnk;
            
        } else {
            $this->stnk = '/storage/uploads/noimage.jpg';
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
            $data = DB::connection('sqlsrv')->table('create_t_m_s')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->join('customers', 'customers.custID', 'create_t_m_s.custID')->select('create_t_m_s.id','pendfNo', 'tmQtyKg','tmQtyKarung','sppbNo','tmCarID','isMktApp','tglMuat','custName','isSecCek')->where('isMktApp','1')->where('pendfNo','like','%' . $this->katakunci . '%')->orderBy('id','desc')->paginate(10);
        
        } else {
         $data = DB::connection('sqlsrv')->table('create_t_m_s')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->join('customers', 'customers.custID', 'create_t_m_s.custID')->select('create_t_m_s.id','pendfNo', 'tmQtyKg','tmQtyKarung','sppbNo','tmCarID','isMktApp','tglMuat','custName','isSecCek')->where('isMktApp','1')->orderBy('id','desc')->paginate(10);
        // dd($data);
        }
        return view('livewire.laptiketmuatapproved',['datatiketmuat' => $data]);
    }
}
