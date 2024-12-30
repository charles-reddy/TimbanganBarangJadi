<?php

namespace App\Livewire;

use App\Models\Createsppb;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Transporter;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Transsppb extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $sppbNo;
    public $bulan;
    public $nomor;
    public $tahun;
    public $kontrakNo;
    #[Validate('required', message: 'Pilih Item Barang')]
    public $itemCode;
    #[Validate('required', message: 'Pilih Customer')]
    public $custID;
    #[Validate('required', message: 'qty  harus diisi')]
    #[Validate('integer', message: 'qty harus dalam angka')]
    public $sppbQtyKg;
    #[Validate('required', message: 'qty Karung harus diisi')]
    #[Validate('integer', message: 'qty Karung harus dalam angka')]
    public $sppbQtyKarung;
    public $openQtyKarung;
    public $openQtyKg;
    public $katakunci;
    public $sortColumn = 'id';
    public $sortDirection = 'desc';
    public $itemName;
    public $custName;
    public $updateData = false;
    public $transID;
    #[Validate('required', message: 'PO No harus diisi')]
    public $poNo;



    public function store()
    {
        $tgl=Carbon::now();
        $this->validate();
        try {
            DB::connection('sqlsrv')->table('createsppbs')->insert([
                'sppbNo' => $this->sppbNo,
                'tglSppb' => $tgl,
                'itemCode' => $this->itemCode,
                'kontrakNo' => $this->kontrakNo,
                'custID' => $this->custID,
                'sppbQtyKg' => $this->sppbQtyKg,
                'sppbQtyKarung' => $this->sppbQtyKarung,
                'openQtyKg' => $this->sppbQtyKg,
                'openQtyKarung' => $this->sppbQtyKarung,
                'poNo' => $this->poNo,

            ]);
            session()->flash('message', 'Data berhasil dimasukkan');
            $this->clear();

        } catch (Exception $e) {
            throw $e;
            // session()->flash('error', 'failed to store data');
            return;
        }

    }

    public function edit($id)
    {
        //  $data = Createsppb::find($id);
        $data = DB::connection('sqlsrv')->table('createsppbs')->join('customers', 'customers.custID', 'createsppbs.custID')->join('products', 'products.itemCode', 'createsppbs.itemCode')->where('id',$id)->first();
        //  dd($data);
        $this->sppbNo = $data->sppbNo;
         $this->kontrakNo = $data->kontrakNo;
         $this->itemName = $data->itemName;
         $this->custName = $data->custName;
         $this->updateData = true;
         $this->sppbQtyKg = $data->sppbQtyKg;
         $this->sppbQtyKarung = $data->sppbQtyKarung;
         $this->transID = $id;
         $this->poNo = $data->poNo;

        //  dd($this->sppbNo);
        
    }

    public function update()
    {
        $tgl=Carbon::now();
        // $data = Createsppb::find($this->transID);
        // dd($data);
        $this->validate();

        try {
            // dd($data->itemCode);
            DB::connection('sqlsrv')->table('createsppbs')->where('id',$this->transID)->update([
                'sppbNo' => $this->sppbNo,
                // 'tglSppb' => $tgl,
                'itemCode' => $this->itemCode,
                'kontrakNo' => $this->kontrakNo,
                'custID' => $this->custID,
                'sppbQtyKg' => $this->sppbQtyKg,
                'sppbQtyKarung' => $this->sppbQtyKarung,
                'poNo' => $this->poNo,
            ]);
            session()->flash('message', 'Data berhasil dimasukkan');
            $this->clear();

        } catch (Exception $e) {
            // throw $e;
            session()->flash('error', 'failed to store data');
            return;
        }
    }

    public function clear()
    {
        $this->sppbNo = '';
        
        $this->kontrakNo = '';
        $this->itemCode = '';
        $this->custID = '';
        $this->sppbQtyKg = '';
        $this->sppbQtyKarung = '';
        $this->poNo = '';
        redirect('/createsppb');
    }

    public function sort($columnName)
    {
        $this->sortColumn = $columnName;
        $this->sortDirection = $this->sortDirection == 'asc'?'desc' : 'asc';

    }

    
    public function render()
    {
        if ($this->katakunci !=null) {
            $data = DB::connection('sqlsrv')->table('createsppbs')->join('customers', 'customers.custID', 'createsppbs.custID')->join('products', 'products.itemCode', 'createsppbs.itemCode')->where('sppbNo','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        } else {
            $data = DB::connection('sqlsrv')->table('createsppbs')->join('customers', 'customers.custID', 'createsppbs.custID')->join('products', 'products.itemCode', 'createsppbs.itemCode')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        }
        // dd($data);

        // $bulan = Carbon::now()->month;
        // $tahun = Carbon::now()->year;
        // $tglawal = date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
        // $tglakhir = date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
        // $data1 = DB::connection('sqlsrv')->table('createsppbs')->wheredate('tglSppb','>=',$tglawal)->wheredate('tglSppb','<=',$tglakhir)->count('id');
        // $nomor = $data1 + 1;
        // $sppbNo = 'SPPB/'. $nomor .'/' . $bulan .'/' . $tahun;
        // $this->sppbNo = $sppbNo;
        //  dd($data1, $tglawal,$tglakhir);

        $angkutan = Transporter::all();
        $barang = Product::where('itemName','like','%gkr%')->orwhere('itemName','like','%gkp%')->orwhere('itemName','like','%mola%')->get();
        $pelanggan = Customer::all();
       
        
        return view('livewire.transsppb', [ 'datasppb' => $data, 'customer' => $pelanggan, 'transporter' => $angkutan,'product' => $barang]); 
    }
}
