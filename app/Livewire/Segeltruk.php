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
        $replaceseal = str_replace("/", "-", $this->spmNo);
        $fotoSealNo1 = $replaceseal . '-1.jpg';
        $fotoSealNo2 = $replaceseal . '-2.jpg';
        $fotoSealNo3 = $replaceseal . '-3.jpg';
        $fotoSealNo4 = $replaceseal . '-4.jpg';
        $fotoSealNo5 = $replaceseal . '-5.jpg';
        $this->validate();

        try {
            // Check if this is a multi-product transaction
            $isMultiProduct = DB::connection('sqlsrv')
                ->table('trscale_headers')
                ->where('id', $this->scaleID)
                ->exists();

            // Get all spm_ids for this transaction (for multi-product)
            $spmIDs = [$this->transID]; // Default: single product
            
            if ($isMultiProduct) {
                // Get all spm_ids from trscale_details with the same header_id
                $spmIDs = DB::connection('sqlsrv')
                    ->table('trscale_details')
                    ->where('header_id', $this->scaleID)
                    ->whereNotNull('spm_id')
                    ->pluck('spm_id')
                    ->toArray();
            }

            if ($this->itemType == 'FG-L') {
                // Update all createspms with related spm_ids
                DB::connection('sqlsrv')->table('createspms')->whereIn('id', $spmIDs)->update([
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

                // Update trscale (single product)
                $updated = DB::connection('sqlsrv')->table('trscale')->where('id', $this->scaleID)->update([
                    'isLoadingDone' => 1,
                    'isLoadingDoneDate' => $tgl,
                ]);

                // If single product not updated, try multi product (trscale_headers)
                if ($updated === 0) {
                    DB::connection('sqlsrv')->table('trscale_headers')->where('id', $this->scaleID)->update([
                        'isLoadingDone' => 1,
                        'isLoadingDoneDate' => $tgl,
                    ]);
                }
            } elseif ($this->jenisTruk1 == 1) {
                // Update all createspms with related spm_ids
                DB::connection('sqlsrv')->table('createspms')->whereIn('id', $spmIDs)->update([
                    'sealNo1' => $this->sealNo1,
                    'sealNo2' => $this->sealNo2,
                    'sealNo3' => $this->sealNo3,
                    'sealNo4' => $this->sealNo4,
                    'sealNo5' => $this->sealNo5,
                    'fotoSealNo1' => 'uploads/segel/' . $fotoSealNo1,

                ]);
                // dd(1);
            } else {
                // Update all createspms with related spm_ids
                DB::connection('sqlsrv')->table('createspms')->whereIn('id', $spmIDs)->update([
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

            if ($this->fotoSealNo1) {
                $this->fotoSealNo1->storeAs('uploads/segel', $fotoSealNo1, 'public');
            }

            if ($this->fotoSealNo2) {
                $this->fotoSealNo2->storeAs('uploads/segel', $fotoSealNo2, 'public');
            }

            if ($this->fotoSealNo3) {
                $this->fotoSealNo3->storeAs('uploads/segel', $fotoSealNo3, 'public');
            }

            if ($this->fotoSealNo4) {
                $this->fotoSealNo4->storeAs('uploads/segel', $fotoSealNo4, 'public');
            }

            if ($this->fotoSealNo5) {
                $this->fotoSealNo5->storeAs('uploads/segel', $fotoSealNo5, 'public');
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

        // Try single product first
        $data = DB::connection('sqlsrv')->table('createspms')
            ->join('customers', 'customers.custID', 'createspms.custID')
            ->join('trscale', 'trscale.spmID', 'createspms.id')
            ->join('products', 'products.itemCode', 'createspms.itemCode')
            ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
            ->select(
                'trscale.id as scaleID',
                'createspms.id as spm_ID',
                'createspms.driver',
                'createspms.spmNo',
                'createspms.carID',
                'customers.custName',
                'products.itemName',
                'products.type',
                'createspms.spmJenisTruk',
                'jenistruks.jenisTruk',
                DB::raw("'single' as trans_type")
            )
            ->where('createspms.id', $id)
            ->first();

        // If not found, try multi product
        if (!$data) {
            $data = DB::connection('sqlsrv')->table('trscale_headers')
                ->join('trscale_details', 'trscale_details.header_id', 'trscale_headers.id')
                ->join('createspms', 'createspms.id', 'trscale_details.spm_id')
                ->join('customers', 'customers.custName', 'trscale_headers.custName')
                ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
                ->select(
                    'trscale_headers.id as scaleID',
                    'createspms.id as spm_ID',
                    'trscale_headers.driver',
                    'createspms.spmNo',
                    'trscale_headers.carID',
                    'trscale_headers.custName',
                    DB::raw("CONCAT(COUNT(DISTINCT trscale_details.itemCode), ' Products') as itemName"),
                    DB::raw("'Multi' as type"),
                    'createspms.spmJenisTruk',
                    'jenistruks.jenisTruk',
                    DB::raw("'multi' as trans_type")
                )
                ->where('createspms.id', $id)
                ->groupBy(
                    'trscale_headers.id',
                    'createspms.id',
                    'trscale_headers.driver',
                    'createspms.spmNo',
                    'trscale_headers.carID',
                    'trscale_headers.custName',
                    'createspms.spmJenisTruk',
                    'jenistruks.jenisTruk'
                )
                ->first();
        }

        // dd($data);
        $this->driver = $data->driver;
        $this->scaleID = $data->scaleID;
        $this->carID = $data->carID;
        $this->custName = $data->custName;
        $this->itemName = $data->itemName;
        $this->itemType = $data->type;
        $this->spmNo = $data->spmNo;
        $this->transID = $data->spm_ID;
        $this->jenisTruk = $data->spmJenisTruk . ' - ' . $data->jenisTruk;
        $this->jenisTruk1 = $data->spmJenisTruk;

        if ($this->jenisTruk1 == 1) {
            $this->fototruk = '/truk/1.png';
        } else if ($this->jenisTruk1 == 2) {
            $this->fototruk = '/truk/2.png';
        } else if ($this->jenisTruk1 == 3) {
            $this->fototruk = '/truk/3.png';
        } else if ($this->jenisTruk1 == 4) {
            $this->fototruk = '/truk/4.png';
        } else if ($this->jenisTruk1 == 5) {
            $this->fototruk = '/truk/5.png';
        } else if ($this->jenisTruk1 == 6) {
            $this->fototruk = '/truk/6.png';
        } else if ($this->jenisTruk1 == 7) {
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
        // ========== DATAGULA: Truk gula yang sudah jam_out tapi belum ada seal ==========
        // Single product query
        $gulaSingle = DB::connection('sqlsrv')->table('createspms')
            ->join('customers', 'customers.custID', 'createspms.custID')
            ->join('trscale', 'trscale.spmID', 'createspms.id')
            ->join('products', 'products.itemCode', 'createspms.itemCode')
            ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
            ->select(
                'createspms.id as id',
                'createspms.driver',
                'createspms.carID',
                'products.itemName',
                'createspms.spmNo',
                'customers.custName',
                'jenistruks.jenisTruk',
                'trscale.jam_in',
                'products.type',
                DB::raw("'single' as trans_type")
            )
            ->whereNotNull('jam_out')
            ->where('type', '<>', 'FG-L')
            ->whereNull('sealNo1');

        // Multi product query
        $gulaMulti = DB::connection('sqlsrv')
            ->table(DB::raw('(
                SELECT 
                    h.id,
                    h.driver,
                    h.carID,
                    h.custName,
                    h.weigh_in_time,
                    COUNT(DISTINCT d.itemCode) as product_count,
                    (SELECT TOP 1 spm_id FROM trscale_details WHERE header_id = h.id) as first_spmID,
                    (SELECT TOP 1 itemType FROM trscale_details WHERE header_id = h.id) as first_type
                FROM trscale_headers h
                LEFT JOIN trscale_details d ON d.header_id = h.id
                WHERE 
                    h.weigh_out_time IS NOT NULL
                GROUP BY h.id, h.driver, h.carID, h.custName, h.weigh_in_time
            ) as multi_data'))
            ->leftJoin('createspms', 'createspms.id', '=', 'multi_data.first_spmID')
            ->leftJoin('jenistruks', 'jenistruks.id', '=', 'createspms.spmJenisTruk')
            ->select(
                'createspms.id as id',
                'multi_data.driver',
                'multi_data.carID',
                DB::raw("CONCAT(multi_data.product_count, ' Products') as itemName"),
                'createspms.spmNo',
                'multi_data.custName',
                'jenistruks.jenisTruk',
                'multi_data.weigh_in_time as jam_in',
                DB::raw("'Multi' as type"),
                DB::raw("'multi' as trans_type")
            )
            ->whereNull('createspms.sealNo1');
            // dd($gulaMulti);

        $datagula = $gulaSingle->unionAll($gulaMulti)->orderBy('id', 'desc')->paginate(5, ['*'], 'gula_page');

        // ========== DATAMOL: Truk molasses yang sudah loading tapi belum ada seal ==========
        // Single product query only (no multi-product for molasses)
        $datamol = DB::connection('sqlsrv')->table('createspms')
            ->join('customers', 'customers.custID', 'createspms.custID')
            ->join('trscale', 'trscale.spmID', 'createspms.id')
            ->join('products', 'products.itemCode', 'createspms.itemCode')
            ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
            ->select(
                'createspms.id as id',
                'createspms.driver',
                'createspms.carID',
                'createspms.spmNo',
                'products.itemName',
                'customers.custName',
                'jenistruks.jenisTruk',
                'trscale.jam_in',
                'products.type',
                DB::raw("'single' as trans_type")
            )
            ->where('type', '=', 'FG-L')
            ->whereNotNull('trscale.isLoading')
            ->whereNull('trscale.isLoadingDone')
            ->whereNull('sealNo1')
            ->orderBy('createspms.id', 'desc')
            ->paginate(5, ['*'], 'mol_page');

        // ========== DONESEGEL: Truk yang sudah selesai disegel ==========
        // Single product query
        $sealSingle = DB::connection('sqlsrv')->table('createspms')
            ->join('customers', 'customers.custID', 'createspms.custID')
            ->join('trscale', 'trscale.spmID', 'createspms.id')
            ->join('products', 'products.itemCode', 'createspms.itemCode')
            ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
            ->select(
                'createspms.id as id',
                'createspms.driver',
                'createspms.carID',
                'createspms.spmNo',
                'products.itemName',
                'customers.custName',
                'jenistruks.jenisTruk',
                'trscale.jam_in',
                'products.type',
                DB::raw("'single' as trans_type")
            )
            ->whereNotNull('trscale.isLoadingDone')
            ->whereNotNull('sealNo1');

        // Multi product query
        $sealMulti = DB::connection('sqlsrv')
            ->table(DB::raw('(
                SELECT 
                    h.id,
                    h.driver,
                    h.carID,
                    h.custName,
                    h.weigh_in_time,
                    COUNT(DISTINCT d.itemCode) as product_count,
                    (SELECT TOP 1 spm_id FROM trscale_details WHERE header_id = h.id) as first_spmID
                FROM trscale_headers h
                LEFT JOIN trscale_details d ON d.header_id = h.id
                WHERE 
                    d.isLoadingDone IS NOT NULL
                GROUP BY h.id, h.driver, h.carID, h.custName, h.weigh_in_time
            ) as multi_data'))
            ->leftJoin('createspms', 'createspms.id', '=', 'multi_data.first_spmID')
            ->leftJoin('jenistruks', 'jenistruks.id', '=', 'createspms.spmJenisTruk')
            ->select(
                'createspms.id as id',
                'multi_data.driver',
                'multi_data.carID',
                'createspms.spmNo',
                DB::raw("CONCAT(multi_data.product_count, ' Products') as itemName"),
                'multi_data.custName',
                'jenistruks.jenisTruk',
                'multi_data.weigh_in_time as jam_in',
                DB::raw("'Multi' as type"),
                DB::raw("'multi' as trans_type")
            )
            ->whereNotNull('createspms.sealNo1');

        // Apply search filter if exists
        if ($this->katakunci != null) {
            $sealSingle->where('createspms.carID', 'like', '%' . $this->katakunci . '%');
            $sealMulti->where('multi_data.carID', 'like', '%' . $this->katakunci . '%');
        }

        $donesegel = $sealSingle->unionAll($sealMulti)->orderBy('id', 'desc')->paginate(5, ['*'], 'seal_page');

        return view('livewire.segeltruk', ['trukgula' => $datagula, 'trukmol' => $datamol, 'truksdhsegel' => $donesegel]);
    }
}
