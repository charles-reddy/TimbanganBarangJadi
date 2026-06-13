<?php

namespace App\Livewire;

use App\Models\JembatanTimbang;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\WithPagination;

class Timbanginmaterial extends Component
{
    use WithPagination;
    use WithFileUploads;
    public $sortColumn = 'jam_reg';
    public $sortDirection = 'desc';

    // Entry mode: 'registered' or 'manual'
    public $entryMode = 'registered';

    public $regNo;
    public $driver;
    public $carID;
    public $suppID;
    public $suppIDRaw; // For manual mode - raw suppID without name
    public $suppSearchQuery = ''; // For live search
    public $showSupplierDropdown = false;
    public $itemCode;
    public $itemCodeRaw; // For manual mode - raw itemCode without name
    public $itemSearchQuery = ''; // For live search
    public $showItemDropdown = false;
    public $doNo;
    public $updateData = false;
    public $jembatanTimbang;
    #[Validate('required', message: 'berat kosong')]
    public $timbangin;
    #[Validate('required', message: 'pilih timbangan')]
    public $timbanganID;
    public $jam_in;
    public $userIDIN;
    public $usernameIN;
    public $transID;
    public $remarks;
    public $id_trscale;
    public $katakunci;
    public $trscaleSelectedID = [];
    public $poNo;


    // Switch between entry modes
    public function switchMode($mode)
    {
        $this->entryMode = $mode;
        $this->clearFields();
    }

    // Clear all input fields
    public function clearFields()
    {
        $this->regNo = '';
        $this->driver = '';
        $this->carID = '';
        $this->suppID = '';
        $this->suppIDRaw = '';
        $this->suppSearchQuery = '';
        $this->showSupplierDropdown = false;
        $this->itemCode = '';
        $this->itemCodeRaw = '';
        $this->itemSearchQuery = '';
        $this->showItemDropdown = false;
        $this->doNo = '';
        $this->poNo = '';
        $this->remarks = '';
        $this->timbangin = '';
        $this->transID = '';
        $this->resetValidation();
    }

    // Handle supplier search input
    public function updatedSuppSearchQuery()
    {
        $this->showSupplierDropdown = strlen($this->suppSearchQuery) > 0;
        $this->suppIDRaw = ''; // Reset selection when typing
    }

    // Select supplier from dropdown
    public function selectSupplier($suppID, $suppName)
    {
        $this->suppIDRaw = $suppID;
        $this->suppSearchQuery = $suppName;
        $this->showSupplierDropdown = false;
    }

    // Handle item search input
    public function updatedItemSearchQuery()
    {
        $this->showItemDropdown = strlen($this->itemSearchQuery) > 0;
        $this->itemCodeRaw = ''; // Reset selection when typing
    }

    // Select item from dropdown
    public function selectItem($itemCode, $itemName)
    {
        $this->itemCodeRaw = $itemCode;
        $this->itemSearchQuery = $itemName;
        $this->showItemDropdown = false;
    }

    // Close dropdown when clicking outside
    public function closeSupplierDropdown()
    {
        $this->showSupplierDropdown = false;
    }

    public function closeItemDropdown()
    {
        $this->showItemDropdown = false;
    }


    public function timbang()
    {
        $this->timbangin = '';
        try {

            $iptimbangan = JembatanTimbang::where('timbanganID', '=', $this->timbanganID)->value('IP');

            // *************** testing timbangan *******************
            // $this->timbangin = 88888;

            // // dd($this->timbanganID);

            // if ($this->timbanganID == 1) {
            // //     // dd('10');
            //     $data = "http://10.20.1.49:3000/api/weight/SCALE_10";
            // } elseif ($this->timbanganID == 2) {
            // //     // dd('9');
            //     $data = "http://10.20.1.49:3000/api/weight/SCALE_09";
            // } else {
            // //     // dd('8');
            //     $data = "http://10.20.1.49:3000/api/weight/SCALE_08";
            // }

            // dd($this->output);
            // *************** testing timbangan *******************

            // dd($this->timbanganID);
            $data = null; // Initialize $data variable
            switch ($this->timbanganID) {
                case 1:
                    $data = "http://10.20.1.49:3000/api/weight/SCALE_10";
                    break;

                case '2':
                    $data = "http://10.20.1.49:3000/api/weight/SCALE_09";
                    break;

                case '3':
                    $data = "http://10.20.1.49:3000/api/weight/SCALE_08";
                    break;

                case '5':
                    $data = "http://10.20.1.49:3000/api/weight/SCALE_07";
                    break;

                default:
                    session()->flash('error', 'Timbangan tidak valid');
                    return;
            }

            if (!$data) {
                session()->flash('error', 'Pilih timbangan terlebih dahulu');
                return;
            }

            $client = new Client();
            // $data = "http://10.20.1.49:3000/api/weight/SCALE_09";
            $response = $client->request('GET', $data);
            $content =  $response->getBody()->getContents();
            $contentarray = json_decode($content, true);
            //    dd($contentarray['weight']);
            $this->timbangin = $contentarray['weight'];
        } catch (Exception $e) {
            session()->flash('error', 'Pastikan Timbangan yg dipilih sesuai');
            return;
        }
    }


    public function store()
    {

        $jam_in = Carbon::now();
        $userIDIN = Auth::user()->id;
        $usernameIN = Auth::user()->username;

        // Validate based on entry mode
        if ($this->entryMode == 'registered') {
            // Validate for registered mode
            $this->validate([
                'regNo' => 'required',
                'timbangin' => 'required',
                'timbanganID' => 'required',
            ], [
                'regNo.required' => 'Pilih nomor registrasi',
                'timbangin.required' => 'Berat kosong',
                'timbanganID.required' => 'Pilih timbangan',
            ]);
        } else {
            // Validate for manual mode
            $this->validate([
                'driver' => 'required',
                'carID' => 'required',
                'suppIDRaw' => 'required',
                'itemCodeRaw' => 'required',
                'timbangin' => 'required',
                'timbanganID' => 'required',
            ], [
                'driver.required' => 'Driver wajib diisi',
                'carID.required' => 'Car ID wajib diisi',
                'suppIDRaw.required' => 'Pilih supplier',
                'itemCodeRaw.required' => 'Pilih produk',
                'timbangin.required' => 'Berat kosong',
                'timbanganID.required' => 'Pilih timbangan',
            ]);
        }

        try {

            $this->jam_in = $jam_in;
            $this->userIDIN = $userIDIN;
            $this->usernameIN = $usernameIN;

            if ($this->entryMode == 'registered') {
                // MODE 1: Update existing record from registration
                DB::connection('sqlsrv')->table('trscaleb19s')->where('id', $this->transID)->update([
                    'remarks' => $this->remarks,
                    'timbangin' => $this->timbangin,
                    'timbanganInID' => $this->timbanganID,
                    'jam_in' => $this->jam_in,
                    'userIDIN' => $this->userIDIN,
                    'usernameIN' => $this->usernameIN,
                    'updated_at' => $this->jam_in,
                ]);
            } else {
                // MODE 2: Insert new record for manual entry
                DB::connection('sqlsrv')->table('trscaleb19s')->insert([
                    'driver' => $this->driver,
                    'carID' => $this->carID,
                    'suppID' => $this->suppIDRaw,
                    'itemCode' => $this->itemCodeRaw,
                    'doNo' => $this->doNo,
                    'poNo' => $this->poNo,
                    'remarks' => $this->remarks,
                    'timbangin' => $this->timbangin,
                    'timbanganInID' => $this->timbanganID,
                    'jam_in' => $this->jam_in,
                    'jam_reg' => $this->jam_in, // Set jam_reg sama dengan jam_in untuk manual entry
                    'userIDIN' => $this->userIDIN,
                    'usernameIN' => $this->usernameIN,
                    'created_at' => $this->jam_in,
                    'updated_at' => $this->jam_in,
                ]);
            }



            session()->flash('message', 'Data berhasil dimasukkan');
            $this->clear();
            redirect('/timbanginmaterial');
        } catch (Exception $e) {

            throw $e;
            // session()->flash('error', 'failed to store data');
            return;
        }
    }



    public function clear()
    {
        $this->driver = '';
        $this->carID = '';
        $this->doNo = '';
        $this->poNo = '';
        $this->remarks = '';
        $this->itemCode = '';
        $this->suppID = '';
        $this->suppIDRaw = '';
        $this->suppSearchQuery = '';
        $this->showSupplierDropdown = false;
        $this->itemCodeRaw = '';
        $this->itemSearchQuery = '';
        $this->showItemDropdown = false;
        $this->updateData = false;
        $this->id_trscale = '';
        $this->trscaleSelectedID = [];
        $this->timbangin = '';
        $this->entryMode = 'registered'; // Reset to default mode



        redirect('/timbanginmaterial');
    }



    #[Computed()]
    public function regdata()
    {
        // $dataspm = DB::connection('sqlsrv')->table('createspms')->join('customers', 'customers.custID', 'createspms.custID')->join('transporters', 'transporters.transpID', 'createspms.transpID')->join('products', 'products.itemCode', 'createspms.itemCode')->where('id',$this->spmNo)->first();
        $registered = DB::connection('sqlsrv')->table('trscaleb19s')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->join('products', 'products.itemCode', 'trscaleb19s.itemCode')->where('id', $this->regNo)->first();

        //  dd($registered);
        $this->driver = $registered->driver;
        $this->carID = $registered->carID;
        $this->suppID = $registered->suppID . '-' . $registered->suppName;
        //  $this->transpID = $registered->transpID .'-'. $registered->transpName; 
        $this->itemCode = $registered->itemCode . '-' . $registered->itemName;
        $this->doNo = $registered->doNo;
        $this->transID = $registered->id;
    }

    public function render()
    {
        if ($this->katakunci != null) {
            $data = DB::connection('sqlsrv')->table('trscaleb19s')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->join('products', 'products.itemCode', 'trscaleb19s.itemCode')->whereNull('netto')->where('suppName', 'like', '%' . $this->katakunci . '%')->orwhere('carID', 'like', '%' . $this->katakunci . '%')->orderby($this->sortColumn, $this->sortDirection)->paginate(5);
        } else {
            $data = DB::connection('sqlsrv')->table('trscaleb19s')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->join('products', 'products.itemCode', 'trscaleb19s.itemCode')->whereNull('netto')->orderby($this->sortColumn, $this->sortDirection)->paginate(5);
        }

        if ($this->regNo != null && $this->entryMode == 'registered') {

            $this->regdata();
        }
        $timbangan = JembatanTimbang::all();

        // Ambil reglist hanya untuk H-3 (3 hari terakhir)
        $tglBatas = Carbon::now()->subDays(3)->startOfDay();
        $reglist = DB::connection('sqlsrv')->table('trscaleb19s')
            ->select('trscaleb19s.id', 'trscaleb19s.carID', 'suppliers.suppName', 'suppliers.suppID')
            ->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')
            ->whereNull('timbangin')
            ->where('jam_reg', '>=', $tglBatas)
            ->orderBy('jam_reg', 'desc')
            ->get();

        // Load supplier dan product list untuk manual mode
        $suppliers = DB::connection('sqlsrv')->table('suppliers')
            ->select('suppID', 'suppName')
            ->when($this->suppSearchQuery, function ($query) {
                $query->where('suppName', 'like', '%' . $this->suppSearchQuery . '%');
            })
            ->orderBy('suppName', 'asc')
            ->limit(10) // Limit results for performance
            ->get();

        $products = DB::connection('sqlsrv')->table('products')
            ->select('itemCode', 'itemName')
            ->when($this->itemSearchQuery, function ($query) {
                $query->where('itemName', 'like', '%' . $this->itemSearchQuery . '%');
            })
            ->orderBy('itemName', 'asc')
            ->limit(10) // Limit results for performance
            ->get();

        return view('livewire.timbanginmaterial', [
            'datatim' => $data,
            'datareg1' => $reglist,
            'timbangan' => $timbangan,
            'suppliers' => $suppliers,
            'products' => $products
        ]);
    }
}
