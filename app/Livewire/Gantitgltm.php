<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Gantitgltm extends Component
{
    use WithPagination;
    public $katakunci;
     #[Validate('required', message: 'Pilih Tiket Muat dari Customer')]
    public $transID;
    public $sppbNo;
    #[Validate('required', message: 'Tgl Muat diisi')]
    public $tglMuat;
    public $custName;
    public $pendfNo;
    public $tmCarID;
    public $tmDriver;
    public $ip;

     public function store()
    {
        
        $this->validate();

        try {
               
            DB::connection('sqlsrv')->table('create_t_m_s')->where('id',$this->transID)->update([
                'tglMuat' => $this->tglMuat,
                
                
            ]);
            
            session()->flash('message', 'Data berhasil dimasukkan');
            redirect('/gantitgltm');
            

        } catch (\Throwable $th) {
            
            
            session()->flash('error', 'gagal menyimpan data');
            
        }
    }

    public function edit($id)
    {
        $data = DB::connection('sqlsrv')->table('create_t_m_s')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->select('create_t_m_s.id','create_t_m_s.pendfNo','create_t_m_s.tmCarID','create_t_m_s.tmDriver','create_t_m_s.noHPDriver','create_t_m_s.tmTranspName','create_t_m_s.tglMuat','products.itemName','customers.custName')->where('create_t_m_s.id', $id)->first();
        
        $this->pendfNo = $data->pendfNo;
        $this->transID = $id;
        $this->custName = $data->custName;
        $this->tglMuat = $data->tglMuat;
        $this->tmCarID = $data->tmCarID;
        $this->tmDriver = $data->tmDriver;
       

        // dd($data->stnk, $data->simKtp);
    }
    
    
    public function render()
    {

        if ($this->katakunci  !=null) {
            $data = DB::connection('sqlsrv')->table('create_t_m_s')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->select('create_t_m_s.id','create_t_m_s.pendfNo','create_t_m_s.tmCarID','create_t_m_s.tmDriver','create_t_m_s.noHPDriver','create_t_m_s.tmTranspName','create_t_m_s.tglMuat','products.itemName','customers.custName')->where('create_t_m_s.pendfNo','like','%' . $this->katakunci . '%')->orderBy('id','desc')->paginate(10);
        
        } else {
            $data = DB::connection('sqlsrv')->table('create_t_m_s')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->select('create_t_m_s.id','create_t_m_s.pendfNo','create_t_m_s.tmCarID','create_t_m_s.tmDriver','create_t_m_s.noHPDriver','create_t_m_s.tmTranspName','create_t_m_s.tglMuat','products.itemName','customers.custName')->whereBetween('tglMuat',[Carbon::now(), Carbon::now()->addDays(+3) ])->orderBy('id','desc')->paginate(10);
        
        }
$this->ip = request()->ip();
        // dd($data);
        return view('livewire.gantitgltm',['datatm' => $data]);
    }
}
