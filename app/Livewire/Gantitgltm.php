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
    public $tglMuat1;
    public $custName;
    public $pendfNo;
    public $tmCarID;
    public $tmDriver;
    public $ip;

    public function store()
    {

        $this->validate();

        try {

            DB::connection('sqlsrv')->table('create_t_m_s')->where('id', $this->transID)->update([
                'tglMuat' => $this->tglMuat,


            ]);

            DB::connection('sqlsrv')->table('tbl_log_rubah_tglMuat')->insert([
                'tmID' => $this->transID,
                'tglMuat' => $this->tglMuat,
                'tglMuat1' => $this->tglMuat1,
                'usrID' => Auth::user()->id,
                'created_at' => Carbon::now(),

            ]);

            session()->flash('message', 'Data berhasil dimasukkan');
            redirect('/gantitgltm');
        } catch (\Throwable $th) {


            session()->flash('error', 'gagal menyimpan data');
        }
    }

    public function clear(){
        redirect('/gantitgltm');
    }


    public function edit($id)
    {
        $data = DB::connection('sqlsrv')->table('create_t_m_s')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->select('create_t_m_s.id', 'create_t_m_s.pendfNo', 'create_t_m_s.tmCarID', 'create_t_m_s.tmDriver', 'create_t_m_s.noHPDriver', 'create_t_m_s.tmTranspName', 'create_t_m_s.tglMuat', 'products.itemName', 'customers.custName')->where('create_t_m_s.id', $id)->first();

        $this->pendfNo = $data->pendfNo;
        $this->transID = $id;
        $this->custName = $data->custName;
        $this->tglMuat = $data->tglMuat;
        $this->tglMuat1 = $data->tglMuat;
        $this->tmCarID = $data->tmCarID;
        $this->tmDriver = $data->tmDriver;


        // dd($data->stnk, $data->simKtp);
    }


    public function render()
    {

        if ($this->katakunci  != null) {
            $data = DB::connection('sqlsrv')->table('create_t_m_s')
                ->join('customers', 'customers.custID', 'create_t_m_s.custID')
                ->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')
                ->join('products', 'products.itemCode', 'create_t_m_s.itemCode')
                ->leftJoin('tbl_log_rubah_tglMuat', function ($join) {
                    $join->on('tbl_log_rubah_tglMuat.tmID', '=', 'create_t_m_s.id')
                        ->whereRaw('tbl_log_rubah_tglMuat.id = (SELECT TOP 1 id FROM tbl_log_rubah_tglMuat WHERE tmID = create_t_m_s.id ORDER BY id DESC)');
                })
                ->leftJoin('users', 'users.id', '=', 'tbl_log_rubah_tglMuat.usrID')
                ->select('create_t_m_s.id', 'create_t_m_s.pendfNo', 'create_t_m_s.tmCarID', 'create_t_m_s.tmDriver', 'create_t_m_s.noHPDriver', 'create_t_m_s.tmTranspName', 'create_t_m_s.tglMuat', 'products.itemName', 'customers.custName', 'tbl_log_rubah_tglMuat.tglMuat as tglMuatLog', 'tbl_log_rubah_tglMuat.tglMuat1 as tglMuat1Log', 'users.name as updatedBy', 'tbl_log_rubah_tglMuat.created_at')
                ->where('create_t_m_s.pendfNo', 'like', '%' . $this->katakunci . '%')
                ->whereBetween('create_t_m_s.tglMuat', [Carbon::now(), Carbon::now()->addDays(+4)])
                ->orderBy('create_t_m_s.id', 'desc')
                ->paginate(10);
        } else {
            $data = DB::connection('sqlsrv')->table('create_t_m_s')
                ->join('customers', 'customers.custID', 'create_t_m_s.custID')
                ->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')
                ->join('products', 'products.itemCode', 'create_t_m_s.itemCode')
                ->leftJoin('tbl_log_rubah_tglMuat', function ($join) {
                    $join->on('tbl_log_rubah_tglMuat.tmID', '=', 'create_t_m_s.id')
                        ->whereRaw('tbl_log_rubah_tglMuat.id = (SELECT TOP 1 id FROM tbl_log_rubah_tglMuat WHERE tmID = create_t_m_s.id ORDER BY id DESC)');
                })
                ->leftJoin('users', 'users.id', '=', 'tbl_log_rubah_tglMuat.usrID')
                ->select('create_t_m_s.id', 'create_t_m_s.pendfNo', 'create_t_m_s.tmCarID', 'create_t_m_s.tmDriver', 'create_t_m_s.noHPDriver', 'create_t_m_s.tmTranspName', 'create_t_m_s.tglMuat', 'products.itemName', 'customers.custName', 'tbl_log_rubah_tglMuat.tglMuat as tglMuatLog', 'tbl_log_rubah_tglMuat.tglMuat1 as tglMuat1Log', 'users.name as updatedBy', 'tbl_log_rubah_tglMuat.created_at')
                ->whereBetween('create_t_m_s.tglMuat', [Carbon::now(), Carbon::now()->addDays(+3)])
                ->orderBy('create_t_m_s.id', 'desc')
                ->paginate(10);
        }
        // $this->ip = request()->ip();
        // dd($data);
        return view('livewire.gantitgltm', ['datatm' => $data]);
    }
}
