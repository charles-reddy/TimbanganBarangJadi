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
    public $kataproduct = [];
    public $sudahPabrik;


    public function store()
    {
        // Validate transID before processing
        if (!is_numeric($this->transID) || empty($this->transID)) {
            session()->flash('error', 'Invalid transaction ID');
            return;
        }

        try {

            DB::connection('sqlsrv')->table('create_t_m_s')->where('id', (int) $this->transID)->update([
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
        // Ensure $id is a valid integer
        if (!is_numeric($id) || empty($id)) {
            session()->flash('error', 'Invalid ID parameter');
            return;
        }

        $id = (int) $id;

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
        // Ensure $id is a valid integer
        if (!is_numeric($id) || empty($id)) {
            session()->flash('error', 'Invalid ID parameter');
            return;
        }

        $id = (int) $id;

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
        // Build the base query with proper type handling
        // Note: tmSppbID is nvarchar(255), custID is int, but join keys are bigint
        $query = DB::connection('sqlsrv')->table('create_t_m_s')
            ->join('createsppbs', function ($join) {
                // Strip whitespace and cast tmSppbID (nvarchar) to bigint
                $join->on('createsppbs.id', '=', DB::raw('TRY_CAST(LTRIM(RTRIM(create_t_m_s.tmSppbID)) AS BIGINT)'));
            })
            ->join('customers', function ($join) {
                // Cast custID (int) to bigint for join
                $join->on('customers.custID', '=', DB::raw('CAST(create_t_m_s.custID AS BIGINT)'));
            })
            ->join('products', 'products.itemCode', '=', 'create_t_m_s.itemCode')
            ->where('isMktApp', '1')
            ->whereNotNull('create_t_m_s.tmSppbID')
            ->where('create_t_m_s.tmSppbID', '!=', '')
            ->whereNotNull('create_t_m_s.custID')
            ->where('create_t_m_s.custID', '!=', '')
            // Filter out records with invalid tmSppbID (like the one with newline)
            ->whereRaw('ISNUMERIC(LTRIM(RTRIM(create_t_m_s.tmSppbID))) = 1');

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

        if (!empty($this->kataproduct)) {
            $query->whereIn('products.itemCode', $this->kataproduct);
        }

        if ($this->sudahPabrik !== null && $this->sudahPabrik !== '') {
            if ($this->sudahPabrik == '1') {
                $query->whereNotNull('isSecCek');
            } else {
                $query->whereNull('isSecCek');
            }
        }

        // Get all products for dropdown
        $products = DB::connection('sqlsrv')->table('products')
            ->select('itemCode', 'itemName')
            ->where('type', '!=', 'NFG')
            ->orderBy('itemName')
            ->get();

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

        return view('livewire.laptiketmuatapproved', ['datatiketmuat' => $data, 'products' => $products]);
    }
}
