<?php

namespace App\Livewire;

use App\Models\antrian;
use App\Models\Createspm as ModelsCreatespm;
use App\Models\packing;
use App\Models\Product;
use App\Models\Transporter;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Riskihajar\Terbilang\Facades\Terbilang;

class Createspm extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $spmNo;
    public $bulan;
    public $nomor;
    public $tahun;
    #[Validate('required', message: 'tiket No dibutuhkan')]
    public $tiketID;

    #[Validate('required', message: 'SPPB No dibutuhkan')]
    public $sppbNo;
    #[Validate('required', message: 'Car ID dibutuhkan')]
    public $carID;
    #[Validate('required', message: 'driver harus diisi')]
    public $driver;
    #[Validate('required', message: 'Seal No dibutuhkan')]
    public $sealNo;
    public $kontainerNo;
    #[Validate('required', message: 'Pilih Item Barang')]
    public $itemCode;
    #[Validate('required', message: 'Pilih Transporter')]
    public $transpID;
    #[Validate('required', message: 'qty  harus diisi')]
    #[Validate('integer', message: 'qty harus dalam angka')]
    public $qtyKg;
    #[Validate('required', message: 'qty Karung harus diisi')]
    #[Validate('integer', message: 'qty Karung harus dalam angka')]
    public $qtyKarung;
    public $terbilangKg;
    public $terbilangKarung;
    public $katakunci;
    public $sortColumn = 'id';
    public $sortDirection = 'desc';
    #[Validate('required', message: 'Pilih Kemasan')]
    public $packingID;

   

    public function store()
    {
        $tgl=Carbon::now();
        //  dd($this->terbilangKarung);
        $this->validate();
        $this->terbilangKg = Terbilang::make($this->qtyKg);
        $this->terbilangKarung = Terbilang::make($this->qtyKarung);
        $datasppb = DB::connection('sqlsrv')->table('createsppbs')->select('openQtyKg','openQtyKarung', 'custID' )->where('id',$this->sppbNo)->get();
        foreach ($datasppb as $item) {
            $opQtyKg = $item->openQtyKg;
            $opQtyKarung = $item->openQtyKarung;
            $custID = $item->custID;
        } 
        
        $opeQtyKg = $opQtyKg - $this->qtyKg;
        $opeQtyKarung = $opQtyKarung - $this->qtyKarung;
        //  dd($custID);
        
        try {
            // dd($this->tiketID, $this->sppbNo);
            DB::connection('sqlsrv')->table('createspms')->insert([
                'spmNo' => $this->spmNo,
                'tglSPM' => $tgl,
                'sppbNo' => $this->sppbNo,
                'tiketID' => $this->tiketID,
                'carID' => $this->carID,
                'driver' => $this->driver,
                'sealNo' => $this->sealNo,
                'kontainerNo' => $this->kontainerNo,
                'itemCode' => $this->itemCode,
                'transpID' => $this->transpID,
                'custID' => $custID,
                'qtyKg' => $this->qtyKg,
                'qtyKarung' => $this->qtyKarung,
                'terbilangKg' => $this->terbilangKg,
                'terbilangKarung' => $this->terbilangKarung,
                'packingID' => $this->packingID,
            ]);

            // DB::connection('mysql')->table('tb_reservasi')->where('no',$this->tiketID)->update([
            //     // 'stts' => 'Cek In',
            //     // 'ptgs' => 'system',
            //     // 'idptgs' => 'system',
               
            // ]);

            
            DB::connection('sqlsrv')->table('createsppbs')->where('id',$this->sppbNo)->update([
                // 'stts' => 'Cek In',
                'openQtyKg' => $opeQtyKg,
                'openQtyKarung' => $opeQtyKarung,
               
            ]);
            session()->flash('message', 'Data berhasil dimasukkan');
            $this->clear();
        } catch (Exception $e) {
            throw $e;
            // session()->flash('error', 'failed to store data');
            return;
        }
    }

    public function sort($columnName)
    {
        $this->sortColumn = $columnName;
        $this->sortDirection = $this->sortDirection == 'asc'?'desc' : 'asc';

    }

    public function clear()
    {
        $this->sppbNo = '';
        $this->carID = '';
        $this->sealNo = '';
        $this->kontainerNo = '';
        $this->itemCode = '';
        $this->transpID = '';
        $this->qtyKg = '';
        $this->qtyKarung = '';
        $this->driver = '';
        $this->packingID = '';
        redirect('/createspm');
    }


    #[Computed()]
    public function sppbdata()
    {
        $datasppb = DB::connection('sqlsrv')->table('createsppbs')->join('customers', 'customers.custID', 'createsppbs.custID')->join('products', 'products.itemCode', 'createsppbs.itemCode')->where('id',$this->sppbNo)->first();
        //  dd($datasppb->itemCode);
          $this->itemCode = $datasppb->itemCode;
          $this->qtyKg = $datasppb->openQtyKg;
          $this->qtyKarung = $datasppb->openQtyKarung;
    }
     

    public function render()
    {   
        if ($this->katakunci !=null) {
            $data = DB::connection('sqlsrv')->table('createspms')->join('transporters', 'transporters.transpID', 'createspms.transpID')->join('products', 'products.itemCode', 'createspms.itemCode')->where('sppbNo','like','%' . $this->katakunci . '%')->orwhere('carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        } else {
            $data = DB::connection('sqlsrv')->table('createspms')->join('transporters', 'transporters.transpID', 'createspms.transpID')->join('products', 'products.itemCode', 'createspms.itemCode')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        }

        if ($this->sppbNo !=null)
        {
            $this->sppbdata();
        }

        // dd($data);
        
        $bulan = Carbon::now()->month;
        $tahun = Carbon::now()->year;
        $tglawal = date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
        $tglakhir = date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
        $tglskr = date('Y-m-d', strtotime(Carbon::now()));
        $data1 = DB::connection('sqlsrv')->table('createspms')->wheredate('tglSpm','>=',$tglawal)->wheredate('tglSpm','<=',$tglakhir)->count('id');
        $nomor = $data1 + 1;
        $spmNo = 'SPM/'. $nomor .'/' . $bulan .'/' . $tahun;
        $this->spmNo = $spmNo;

        $tiket = DB::connection('mysql')->table('tb_reservasi')->select('no','nodo','stts','tgldaf','token','cust')->where('stts','=','Daftar')->where('tgldaf','=',$tglskr)->get();
        //  dd($tiket);
        $angkutan = Transporter::all();
        $barang = Product::all();
        $paking = packing::all();
        $getsppb = DB::connection('sqlsrv')->table('createsppbs')->join('customers', 'customers.custID', 'createsppbs.custID')->select('id','sppbNo','custName')->where('openQtyKg','>',0)->get();
        //   dd($getsppb);
        return view('livewire.createspm', [ 'dataspm' => $data, 'transporter' => $angkutan,'product' => $barang,'listsppb' => $getsppb,'antrian' => $tiket,'kemasan' => $paking]);
    }
}
