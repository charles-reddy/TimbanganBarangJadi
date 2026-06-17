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
    public $kataproduct;


    public function store()
    {


        try {

            DB::connection('sqlsrv')->table('create_t_m_s')->where('id', $this->transID)->update([
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
        $this->ip = substr(request()->ip(), 0, 2);
        $data = DB::connection('sqlsrv')->table('create_t_m_s')->where('id', $id)->first();
        // dd($data->simKtp);
        $this->tiketMuat = $data->pendfNo;
        $this->transID = $id;
        if ($data->simKtp != null) {

            if ($this->ip == '10.20.3.9') {
                // dd('local');
                $this->simKtp = 'http://10.20.1.64:8104/storage/' . $data->simKtp;
            } else {
                // dd('outside');
                $this->simKtp = 'https://customer.appktm.com/storage/' . $data->simKtp;
            }
        } else {
            if ($this->ip == '10.20.3.9') {
                // dd('local');
                $this->simKtp = 'http://10.20.1.64:8104/storage/uploads/noimage.jpg';
            } else {
                // dd('outside');
                $this->simKtp = 'https://customer.appktm.com/storage/uploads/noimage.jpg';
            }
        }

        if ($data->stnk != null) {
            if ($this->ip == '10.20.3.9') {
                // dd('local');
                $this->stnk = 'http://10.20.1.64:8104/storage/' . $data->stnk;
            } else {
                $this->stnk = 'https://customer.appktm.com/storage/' . $data->stnk;
            }
        } else {
            if ($this->ip == '10.20.3.9') {
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
        // Build the base query
        $query = DB::connection('sqlsrv')->table('create_t_m_s')
            ->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')
            ->join('customers', 'customers.custID', 'create_t_m_s.custID')
            ->join('products', 'products.itemCode', 'create_t_m_s.itemCode')
            ->where('isMktApp', '1');

        // Apply filters conditionally
        if ($this->katakunci) {
            $query->where('pendfNo', 'like', '%' . $this->katakunci . '%');
        }

        if ($this->katacust) {
            $query->where('custName', 'like', '%' . $this->katacust . '%');
        }

        if ($this->tglMuat) {
            $query->whereDate('tglMuat', '=', $this->tglMuat);
        }

        if ($this->kataproduct) {
            $query->where('products.itemName', 'like', '%' . $this->kataproduct . '%');
        }

        // Select fields and paginate
        $data = $query->select(
            'create_t_m_s.id',
            'pendfNo',
            'tmQtyKg',
            'tmQtyKarung',
            'sppbNo',
            'tmCarID',
            'isMktApp',
            'tglMuat',
            'custName',
            'isSecCek',
            'tmTranspName',
            'isSecCekDate',
            'itemName'
        )
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.laptiketmuatapproved', ['datatiketmuat' => $data]);
    }
}
