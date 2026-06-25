<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\WithPagination;

class Createpgi extends Component
{
    use WithFileUploads;
    use WithPagination;
    public $katakunci;
    public $driver;
    public $carID;
    public $spmID;
    public $spmNo;
    public $custName;
    public $custID;
    public $doNo;
    public $itemName;
    public $timbangin;
    #[Validate('required', message: 'Pilih kendaraan Muat dari Customer')]
    public $transID;
    #[Rule('max:1024', message: 'Foto PGI maks 1 MB')]
    #[Validate('required', message: 'Silahkan Upload foto PGI')]
    #[Validate('image', message: 'PGI harus image')]
    public $buktiPGI;
    public $itemCode;
    public $updateData;
    public $id_trscale;
    public $kontainerNo;


    public function update()
    {
        $tgl = Carbon::now();
        $userIDIN = Auth::user()->id;
        $usernameIN = Auth::user()->username;
        $replacespm = str_replace("/", "-", $this->spmNo);
        $replacespm1 = $replacespm . '.jpg';

        $this->validate();

        try {
            // Debug: Log nilai spmID
            Log::info('Updating PGI - transID: ' . $this->transID . ', spmID: ' . $this->spmID . ', spmNo: ' . $this->spmNo);

            // Pastikan spmID ada
            if (!$this->spmID) {
                session()->flash('error', 'SPM ID tidak ditemukan');
                return;
            }

            // Verifikasi data sebelum update
            $existingData = DB::connection('sqlsrv')->table('createspms')
                ->where('id', $this->spmID)
                ->first();

            if (!$existingData) {
                Log::warning('SPM ID not found in createspms: ' . $this->spmID);
                session()->flash('error', 'Data SPM tidak ditemukan dengan ID: ' . $this->spmID);
                return;
            }

            Log::info('Existing SPM data - spmNo: ' . $existingData->spmNo . ', carID: ' . $existingData->carID);

            // Update buktiPGI di createspms
            $affected = DB::connection('sqlsrv')->table('createspms')
                ->where('id', $this->spmID)
                ->update([
                    'buktiPGI' => 'uploads/pgi/' . $replacespm1,
                ]);

            Log::info('Rows affected: ' . $affected);

            if ($this->buktiPGI) {
                $this->buktiPGI->storeAs('uploads/pgi', $replacespm1, 'public');
            }

            if ($affected > 0) {
                session()->flash('message', 'Data berhasil dimasukkan');
            } else {
                session()->flash('error', 'Data tidak ditemukan atau tidak ada perubahan');
            }

            $this->clear();
            redirect('/createpgi');
        } catch (Exception $e) {
            Log::error('Error updating PGI: ' . $e->getMessage());
            session()->flash('error', 'Failed to store data: ' . $e->getMessage());
            return;
        }
    }


    public function edit($id, $transType = null)
    {
        Log::info('Edit called with ID: ' . $id . ', trans_type: ' . $transType);

        // Jika trans_type adalah 'multi', langsung cari di multi product
        if ($transType === 'multi') {
            Log::info('Directly checking multi product based on trans_type');

            $data = DB::connection('sqlsrv')->table('trscale_details')
                ->join('trscale_headers', 'trscale_headers.id', 'trscale_details.header_id')
                ->join('createspms', 'createspms.id', '=', 'trscale_details.spm_id')
                ->where('trscale_details.id', $id)
                ->select(
                    'trscale_headers.driver',
                    'trscale_headers.carID',
                    'trscale_headers.custName',
                    'trscale_details.itemName',
                    'trscale_details.itemCode',
                    'trscale_details.spm_id as spmID',
                    'createspms.id as createspms_id',
                    'createspms.spmNo',
                    'createspms.carID as spm_carID'
                )
                ->first();

            if ($data) {
                Log::info('Multi product edit - trsID: ' . $id . ', spmID (trscale_details.spm_id): ' . $data->spmID . ', createspms.id: ' . $data->createspms_id . ', spmNo: ' . $data->spmNo);

                $this->driver = $data->driver;
                $this->carID = $data->carID;
                $this->custName = $data->custName;
                $this->custID = null;
                $this->doNo = '';  // Multi product tidak memiliki doNo
                $this->itemName = $data->itemName;
                $this->itemCode = $data->itemCode ?? '';
                $this->transID = $id;
                $this->spmID = $data->spmID;
                $this->spmNo = $data->spmNo ?? 'NO-SPM';
                $this->updateData = true;
                $this->id_trscale = $id;

                Log::info('Loaded to component - this->spmID: ' . $this->spmID);
                return;
            }
        }

        // Coba cari di single product dulu
        $data = DB::connection('sqlsrv')->table('createspms')
            ->join('trscale', 'trscale.spmID', 'createspms.id')
            ->where('trscale.id', $id)
            ->first();

        if ($data) {
            Log::info('Found in single product - spmID: ' . $data->spmID);

            $this->driver = $data->driver;
            $this->carID = $data->carID;
            $this->custID = $data->custID;
            $this->doNo = $data->doNo;

            $this->transID = $id;
            $custN = Customer::where('custID', $this->custID)->value('custName');
            $this->custName = $custN;
            $this->itemCode = $data->itemCode;
            $itemC = Product::where('itemCode', $this->itemCode)->value('ItemName');
            $this->itemName = $itemC;
            $this->updateData = true;
            $this->id_trscale = $id;
            $this->kontainerNo = $data->kontainerNo;
            $this->spmID = $data->spmID;
            $this->spmNo = $data->spmNo;
        } else {
            Log::info('Not found in single product');
        }
    }

    public function clear()
    {

        redirect('/createpgi');
    }

    public function render()
    {
        // Query untuk single product (trscale)
        $singleQuery = DB::connection('sqlsrv')->table('trscale')
            ->join('createspms', 'createspms.id', 'trscale.spmID')
            ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
            ->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')
            ->join('products', 'products.itemCode', 'trscale.itemCode')
            ->join('customers', 'customers.custID', 'trscale.custID')
            ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
            ->select(
                'createspms.id as spmID',
                'createspms.sealNo1',
                'createspms.driver',
                'createspms.carID',
                'createspms.spmNo',
                'products.itemName',
                'customers.custName',
                'jenistruks.jenisTruk',
                'trscale.jam_in',
                'products.type',
                'trscale.id as trsID',
                'trscale.jam_out',
                'trscale.timbangin',
                'trscale.timbangout',
                'trscale.netto',
                'trscale.avgkarung',
                'createspms.sealNo',
                'createsppbs.sppbNo',
                'create_t_m_s.pendfNo',
                'createspms.buktiPGI',
                DB::raw("'single' as trans_type"),
                DB::raw("NULL as header_id")
            )
            ->whereNull('buktiPGI')
            ->whereNotNull('netto')
            ->whereNotNull('createspms.sealNo1');

        // Apply search filter untuk single product
        if (strlen($this->katakunci)) {
            $singleQuery = $singleQuery->where('createspms.carID', 'like', "%{$this->katakunci}%");
        }

        // Query untuk multi product (trscale_headers & trscale_details)
        // Menampilkan per detail transaksi, bukan gabungan
        $multiQuery = DB::connection('sqlsrv')->table('trscale_headers')
            ->join('trscale_details', 'trscale_details.header_id', 'trscale_headers.id')
            ->leftJoin('createspms', 'createspms.id', '=', 'trscale_details.spm_id')
            ->leftJoin('createsppbs', 'createsppbs.id', '=', 'trscale_details.sppb_id')
            ->leftJoin('create_t_m_s', 'create_t_m_s.id', '=', 'createspms.tiketID')
            ->leftJoin('jenistruks', 'jenistruks.id', '=', 'createspms.spmJenisTruk')
            ->select(
                'trscale_details.spm_id as spmID',
                'createspms.sealNo1',
                'trscale_headers.driver',
                'trscale_headers.carID',
                'createspms.spmNo',
                DB::raw("CONCAT('[MULTI] ', trscale_details.itemName) as itemName"),
                'trscale_headers.custName',
                'jenistruks.jenisTruk',
                'trscale_headers.weigh_in_time as jam_in',
                'trscale_details.itemType as type',
                'trscale_details.id as trsID',
                'trscale_headers.weigh_out_time as jam_out',
                'trscale_headers.tare_weight as timbangin',
                'trscale_headers.gross_weight as timbangout',
                'trscale_details.actual_weight as netto',
                'trscale_details.avg_per_karung as avgkarung',
                'createspms.sealNo',
                'createsppbs.sppbNo',
                'create_t_m_s.pendfNo',
                'createspms.buktiPGI',
                DB::raw("'multi' as trans_type"),
                'trscale_headers.id as header_id'
            )
            ->whereNotNull('trscale_headers.net_weight')
            ->whereNotNull('createspms.sealNo1')
            ->whereNull('createspms.buktiPGI');

        // Apply search filter untuk multi product
        if (strlen($this->katakunci)) {
            $multiQuery = $multiQuery->where('trscale_headers.carID', 'like', "%{$this->katakunci}%");
        }

        // Gabungkan single dan multi product
        $sdhout = $singleQuery->unionAll($multiQuery)
            ->orderBy('spmID', 'desc')
            ->paginate(10);

        return view('livewire.createpgi', ['sdhout' => $sdhout]);
    }
}
