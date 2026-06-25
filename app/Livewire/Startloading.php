<?php

namespace App\Livewire;

use App\Models\Trscale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Startloading extends Component
{
    use WithPagination;
    public $katakunci;
    public $driver;
    public $carID;
    public $custName;
    public $itemName;
    public $timbangin;
    public $transType; // 'single' atau 'multi' untuk identify source table
    #[Validate('required', message: 'Pilih kendaraan Muat dari Customer')]
    public $transID;


    public function store()
    {
        $tgl = Carbon::now();
        $userIDApp = Auth::user()->id;
        $usernameApp = Auth::user()->username;
        $this->validate();

        try {
            if ($this->transType === 'single') {
                // Update single product transaction (trscale)
                DB::connection('sqlsrv')->table('trscale')->where('id', $this->transID)->update([
                    'isLoading' => 1,
                    'isLoadingDate' => $tgl,
                ]);
            } else {
                // Update multi product transaction (trscale_headers dan trscale_details)
                DB::connection('sqlsrv')->table('trscale_headers')->where('id', $this->transID)->update([
                    'isLoading' => 1,
                    'isLoadingDate' => $tgl,
                ]);

                // Update all details untuk header ini
                DB::connection('sqlsrv')->table('trscale_details')->where('header_id', $this->transID)->update([
                    'isLoading' => 1,
                    'isLoadingDate' => $tgl,
                ]);
            }

            session()->flash('message', 'Silahkan loading barang');
            $this->clear();
            redirect('/startloading');
        } catch (\Throwable $th) {
            session()->flash('error', 'Gagal menyimpan data: ' . $th->getMessage());
        }
    }


    public function clear()
    {
        redirect('/startloading');
    }

    public function edit($id, $type = 'single')
    {
        $this->transType = $type;

        if ($type === 'single') {
            // Load dari trscale (single product)
            $data = DB::connection('sqlsrv')->table('trscale')
                ->join('customers', 'customers.custID', 'trscale.custID')
                ->join('products', 'products.itemCode', 'trscale.itemCode')
                ->where('trscale.id', $id)
                ->select(
                    'trscale.driver',
                    'trscale.carID',
                    'trscale.timbangin',
                    'customers.custName',
                    'products.itemName'
                )
                ->first();

            if ($data) {
                $this->driver = $data->driver;
                $this->carID = $data->carID;
                $this->custName = $data->custName;
                $this->itemName = $data->itemName;
                $this->timbangin = $data->timbangin;
            }
        } else {
            // Load dari trscale_headers (multi product)
            $data = DB::connection('sqlsrv')->table('trscale_headers')
                ->where('id', $id)
                ->first();

            // Get product list
            $products = DB::connection('sqlsrv')->table('trscale_details')
                ->where('header_id', $id)
                ->pluck('itemName')
                ->toArray();

            if ($data) {
                $this->driver = $data->driver;
                $this->carID = $data->carID;
                $this->custName = $data->custName ?? $data->custID;
                $this->itemName = count($products) . ' Products: ' . implode(', ', array_slice($products, 0, 3)) . (count($products) > 3 ? '...' : '');
                $this->timbangin = $data->tare_weight;
            }
        }

        $this->transID = $id;
    }

    public function render()
    {
        // Query untuk single product (trscale)
        $singleQuery = DB::connection('sqlsrv')->table('trscale')
            ->join('customers', 'customers.custID', '=', 'trscale.custID')
            ->join('createspms', 'createspms.id', '=', 'trscale.spmID')
            ->join('products', 'products.itemCode', '=', 'trscale.itemCode')
            ->select(
                'trscale.id as id',
                'trscale.driver',
                'trscale.carID',
                'trscale.timbangin as weight',
                'trscale.jam_in',
                'customers.custName',
                'products.itemName',
                DB::raw("'single' as trans_type")
            )
            ->whereNull('trscale.isLoading')
            ->whereDate('trscale.jam_in', '>', Carbon::now()->addDays(-5));

        // Query untuk multi product (trscale_headers)
        $multiQuery = DB::connection('sqlsrv')->table('trscale_headers')
            ->leftJoin('trscale_details', 'trscale_details.header_id', '=', 'trscale_headers.id')
            ->select(
                'trscale_headers.id as id',
                'trscale_headers.driver',
                'trscale_headers.carID',
                'trscale_headers.tare_weight as weight',
                'trscale_headers.weigh_in_time as jam_in',
                'trscale_headers.custName',
                DB::raw("CONCAT(COUNT(DISTINCT trscale_details.itemCode), ' Products') as itemName"),
                DB::raw("'multi' as trans_type")
            )
            ->whereNull('trscale_headers.isLoading')
            ->whereDate('trscale_headers.weigh_in_time', '>', Carbon::now()->addDays(-5))
            ->groupBy(
                'trscale_headers.id',
                'trscale_headers.driver',
                'trscale_headers.carID',
                'trscale_headers.tare_weight',
                'trscale_headers.weigh_in_time',
                'trscale_headers.custName'
            );

        // dd($multiQuery);

        // Combine queries
        if ($this->katakunci != null) {
            $singleQuery->where('trscale.carID', 'like', '%' . $this->katakunci . '%');
            $multiQuery->where('trscale_headers.carID', 'like', '%' . $this->katakunci . '%');
        }

        // Union and paginate
        $data = $singleQuery
            ->unionAll($multiQuery)
            ->orderBy('jam_in', 'desc')
            ->paginate(10);

        return view('livewire.startloading', ['siaploading' => $data]);
    }
}
