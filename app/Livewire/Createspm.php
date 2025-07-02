<?php

namespace App\Livewire;

use App\Models\antrian;
use App\Models\Createspm as ModelsCreatespm;
use App\Models\packing;
use App\Models\Product;
use App\Models\Transporter;
use BcMath\Number;
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
    public $spmNo1;
    public $sppbID;
    public $bulan;
    public $nomor;
    public $tahun;
    #[Validate('required', message: 'DN No dibutuhkan')]
    public $dnNo;
    // #[Validate('required', message: 'tiket No dibutuhkan')]
    public $tiketID;
    // #[Validate('required', message: ' No Tiket Muat dibutuhkan')]
    public $tiketMuat;
    public $tiketMuat1;
    public $itemName;

    // #[Validate('required', message: 'SPPB No dibutuhkan')]
    public $sppbNo;
    public $sppbNo1;
    #[Validate('required', message: 'Car ID dibutuhkan')]
    public $carID;
    #[Validate('required', message: 'driver harus diisi')]
    public $driver;
    #[Validate('required', message: 'Seal No dibutuhkan')]
    public $sealNo;
    public $kontainerNo;
    #[Validate('required', message: 'Pilih Item Barang')]
    public $itemCode;
    // #[Validate('required', message: 'Pilih Transporter')]
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
    public $sortColumn = 'createspms.id';
    public $sortDirection = 'desc';
    #[Validate('required', message: 'Pilih Kemasan')]
    public $packingID;
    public $isDisabled = false;
    public $edited = 0;
    public $transID;
    public $updateData = false;
    public $openQtyKarung;
    public $openQtyKg;
    public $AwalQtyKg;
    public $AwalOpenQtyKg;
    public $AwalQtyKarung;
    public $AwalOpenQtyKarung;
    public $custID;
    public $custName;
    #[Validate('required', message: 'Local atau Export?')]
    public $isExport;
    #[Validate('required', message: 'Pilih Jenis Truk')]
    public $tmJenisTruk;
    public $itemType;

    

    

    public function store()
    {
        $tgl=Carbon::now();
        //  dd($this->terbilangKarung);
        $this->validate();
        $this->terbilangKg = Terbilang::make($this->qtyKg);
        $this->terbilangKarung = Terbilang::make($this->qtyKarung);
        
        //  dd($custID);
        
        try {
            // dd($this->tiketID, $this->sppbNo);
            DB::connection('sqlsrv')->table('createspms')->insert([
                'spmNo' => $this->spmNo,
                'tglSPM' => $tgl,
                'sppbNo' => $this->sppbID,
                'tiketID' => $this->tiketMuat,
                'carID' => $this->carID,
                'driver' => $this->driver,
                'sealNo' => $this->sealNo,
                'kontainerNo' => $this->kontainerNo,
                'itemCode' => $this->itemCode,
                'transpID' => $this->transpID,
                'custID' => $this->custID,
                'qtyKg' => $this->qtyKg,
                'qtyKarung' => $this->qtyKarung,
                'terbilangKg' => $this->terbilangKg,
                'terbilangKarung' => $this->terbilangKarung,
                'packingID' => $this->packingID,
                'dnNo' => $this->dnNo,
                'isExport' => $this->isExport,
                'spmJenisTruk' => $this->tmJenisTruk
            ]);

            DB::connection('sqlsrv')->table('create_t_m_s')->where('id',$this->tiketMuat)->update([
                'isSpm' => 1
            ]);
            
            // DB::connection('mysql')->table('tb_reservasi')->where('no',$this->tiketID)->update([
            //     // 'stts' => 'Cek In',
            //     // 'ptgs' => 'system',
            //     // 'idptgs' => 'system',
               
            // ]);

            
            
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

    public function edit($id)
    {
        $data = DB::connection('sqlsrv')->table('createspms')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('customers', 'customers.custID', 'createspms.custID')->join('products', 'products.itemCode', 'createspms.itemCode')->join('packings', 'packings.packingid', 'createspms.packingID')->select('createspms.id','createspms.spmNo','createspms.tiketID','createspms.tglSpm','createspms.itemCode','createspms.transpID','createspms.custID','createspms.qtyKarung','createspms.terbilangkarung','createspms.qtyKg','createspms.terbilangKg','createspms.qtyKg','createspms.packingID','createspms.carID','createspms.driver','createspms.dnNo','createspms.remarks'
        ,'createspms.sealNo','createspms.kontainerNo','createspms.isExport','createspms.isIN','createsppbs.tglSppb','createsppbs.sppbNo','createsppbs.openQtyKarung','createsppbs.openQtyKg','createsppbs.kontrakNo','createsppbs.poNo','createsppbs.sppbQtyKg','createsppbs.sppbQtyKarung'
        ,'create_t_m_s.tmSppbID','create_t_m_s.pendfNo','create_t_m_s.tglDaftar','create_t_m_s.tmCarID','create_t_m_s.tmDriver','create_t_m_s.noHpDriver','create_t_m_s.jenisTruk','create_t_m_s.tmTranspName','create_t_m_s.tmQtyKarung','create_t_m_s.tmQtyKg','create_t_m_s.tglMuat'
        ,'create_t_m_s.simKtp','create_t_m_s.stnk','create_t_m_s.isSpm','create_t_m_s.isMktApp','create_t_m_s.isMktAppID','create_t_m_s.isAppDate','create_t_m_s.isSecCek','create_t_m_s.isSecCekdate','customers.custName'
        ,'customers.custAdd','products.itemName','products.deduction','products.type','products.uom','packings.packingName','createspms.spmJenisTruk', 'products.type')->where('createspms.id',$id)->first();
        //   dd($data);
        $this->edited = 1;
        $this->spmNo1 = $data->spmNo;
        $this->sppbNo = $data->sppbNo;
        $this->driver = $data->driver;
        $this->sealNo = $data->sealNo;
        $this->kontainerNo = $data->kontainerNo;
        $this->qtyKarung = $data->qtyKarung;
        $this->openQtyKarung = $data->openQtyKarung;
        $this->qtyKg = $data->qtyKg;
        $this->dnNo = $data->dnNo;
        $this->openQtyKg = $data->openQtyKg;
        $this->carID = $data->carID;
        $this->tiketMuat1 = $data->pendfNo;
        $this->itemCode = $data->itemName;
        $this->packingID = $data->packingName;
        $this->updateData = true;
        $this->isDisabled = true;
        $this->transID = $id;
        $this->AwalQtyKg = $data->qtyKg;
        $this->AwalOpenQtyKg = $data->openQtyKg;
        $this->AwalQtyKarung = $data->qtyKarung;
        $this->AwalOpenQtyKarung = $data->openQtyKarung;
        $this->isExport = $data->isExport;
        $this->tmJenisTruk = $data->spmJenisTruk;
        $this->custName = $data->custName;
        $this->itemName = $data->itemName;
        $this->itemType = $data->type;
        
        
        
        
         
    }

    public function hitung()
    {
        try {
            if ($this->qtyKg  )
            {
                $selisihkg = $this->qtyKg -  $this->AwalQtyKg;
                $this->openQtyKg =  $this->AwalOpenQtyKg -  $selisihkg;
                
            } else {
                session()->flash('error', 'failed to calculate data');
                return;
            }

            if ($this->qtyKarung )
            {
                
                $selisihkarung = $this->qtyKarung -  $this->AwalQtyKarung;
                $this->openQtyKarung =  $this->AwalOpenQtyKarung -  $selisihkarung;
            } else {
                session()->flash('error', 'failed to calculate data');
                return;
            }

            
            
        } catch (Exception $e) {
            // throw $e;
            session()->flash('error', 'failed to calculate data');
            return;
        }
        
        
    }

    public function update()
    {
        $tgl=Carbon::now();
        // $data = Createsppb::find($this->transID);
        // dd($data);
        $this->validate();
        // dd($this->openQtyKg, $this->openQtyKarung,$this->sppbNo1);
        try {
            // dd($data->itemCode);
            DB::connection('sqlsrv')->table('createspms')->where('id',$this->transID)->update([
                'carID' => $this->carID,
                // 'tglSppb' => $tgl,
                'driver' => $this->driver,
                'sealNo' => $this->sealNo,
                'dnNo' => $this->dnNo,
                'kontainerNo' => $this->kontainerNo,
                'qtyKg' => $this->qtyKg,
                'qtyKarung' => $this->qtyKarung,
                'isExport' => $this->isExport,
                'spmJenisTruk' => $this->tmJenisTruk

            ]);

            // DB::connection('sqlsrv')->table('createsppbs')->where('sppbNo',$this->sppbNo1)->update([
            //     // 'stts' => 'Cek In',
            //     'openQtyKg' => $this->openQtyKg,
            //     'openQtyKarung' => $this->openQtyKarung,
               
            // ]);
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
        $this->carID = '';
        $this->sealNo = '';
        $this->kontainerNo = '';
        $this->itemCode = '';
        $this->transpID = '';
        $this->qtyKg = '';
        $this->qtyKarung = '';
        $this->driver = '';
        $this->packingID = '';
        $this->dnNo = '';
        redirect('/createspm');
    }


    #[Computed()]
    public function tiketmuatdata()
    {
        $datamuat = DB::connection('sqlsrv')->table('create_t_m_s')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->select('create_t_m_s.id','create_t_m_s.tmSppbID','create_t_m_s.pendfNo','create_t_m_s.tmCarID','create_t_m_s.tmDriver','create_t_m_s.noHPDriver','create_t_m_s.tmTranspName','create_t_m_s.tmQtyKarung','create_t_m_s.tmQtyKg','create_t_m_s.tglMuat','create_t_m_s.jenisTruk','products.itemName','products.itemCode','customers.custName','customers.custID','createsppbs.sppbNo','create_t_m_s.tmQtyKarung','create_t_m_s.tmQtyKg','create_t_m_s.isSecCek', 'products.type')->where('create_t_m_s.id',$this->tiketMuat)->whereNotNull('create_t_m_s.isSecCek')->first();
        //  dd($datamuat);
        $this->sppbNo = $datamuat->sppbNo;
        $this->sppbID = $datamuat->tmSppbID;
        $this->carID = $datamuat->tmCarID;
        $this->driver = $datamuat->tmDriver;
        $this->itemName = $datamuat->itemName;
        $this->itemCode = $datamuat->itemCode;
        $this->qtyKarung = $datamuat->tmQtyKarung;
        $this->qtyKg = $datamuat->tmQtyKg;
        $this->custID = $datamuat->custID;
        $this->custName = $datamuat->custName;
        $this->tmJenisTruk = $datamuat->jenisTruk;
        $this->itemType = $datamuat->type;

          
    }

    
     

    public function render()
    {   
        if ($this->katakunci !=null) {
            $data = DB::connection('sqlsrv')->table('createspms')->select('createspms.id', 'createsppbs.sppbNo', 'createspms.tglSpm','createspms.spmNo','createspms.tiketID','createspms.carID','createspms.spmNo','createspms.driver','createspms.sealNo','createspms.isExport','createspms.kontainerNo','createspms.itemCode','createspms.packingID','createspms.qtyKg','createspms.qtyKarung','products.itemName','createsppbs.openQtyKg','createsppbs.openQtyKarung','packings.packingName', 'create_t_m_s.pendfNo')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'createspms.itemCode')->join('packings', 'packings.packingID', 'createspms.packingID')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->where('createsppbs.sppbNo','like','%' . $this->katakunci . '%')->orwhere('createspms.carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        } else {
            $data = DB::connection('sqlsrv')->table('createspms')->select('createspms.id', 'createsppbs.sppbNo', 'createspms.tglSpm','createspms.spmNo','createspms.tiketID','createspms.carID','createspms.spmNo','createspms.driver','createspms.sealNo','createspms.isExport','createspms.kontainerNo','createspms.itemCode','createspms.packingID','createspms.qtyKg','createspms.qtyKarung','products.itemName','createsppbs.openQtyKg','createsppbs.openQtyKarung','packings.packingName', 'create_t_m_s.pendfNo')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'createspms.itemCode')->join('packings', 'packings.packingID', 'createspms.packingID')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            // $data = DB::connection('sqlsrv')->table('createspms')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'createspms.itemCode')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        
        }
        //  dd($data);
        if ($this->tiketMuat !=null)
        {
            
           $this->tiketmuatdata();
        }

        // $this->updateqty();
        $truk = DB::connection('sqlsrv')->table('jenistruks')->get();

        $bulan = Carbon::now()->month;
        $tahun = Carbon::now()->year;
        $tglawal = date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
        $tglakhir = date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
        $tglskr = date('Y-m-d', strtotime(Carbon::now()));
        $data1 = DB::connection('sqlsrv')->table('createspms')->wheredate('tglSpm','>=',$tglawal)->wheredate('tglSpm','<=',$tglakhir)->count('id');
        $nomor = $data1 + 1;
        $spmNo = 'SPM/'. $nomor .'/' . $bulan .'/' . $tahun;
        // dd($this->edited);
        if ($this->edited == 0)
        {
            $this->spmNo =  $spmNo;
        } else {
            $this->spmNo = '';
        }
        $tiket = DB::connection('mysql')->table('tb_reservasi')->select('no','nodo','stts','tgldaf','token','cust')->where('stts','=','Daftar')->where('tgldaf','=',$tglskr)->get();
        //  dd($tiket);
        $angkutan = Transporter::all();
        $barang = Product::where('itemName','like','%gkr%')->orwhere('itemName','like','%gkp%')->orwhere('itemName','like','%mola%')->get();
        // dd($barang);
        $paking = packing::all();
        $getsppb = DB::connection('sqlsrv')->table('createsppbs')->join('customers', 'customers.custID', 'createsppbs.custID')->select('id','sppbNo','custName')->where('openQtyKg','>',0)->get();
        //   dd($getsppb);
        $getTm = DB::connection('sqlsrv')->table('create_t_m_s')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->select('create_t_m_s.id','create_t_m_s.tmSppbID','create_t_m_s.pendfNo','create_t_m_s.tmCarID','create_t_m_s.tmDriver','create_t_m_s.noHPDriver','create_t_m_s.tmTranspName','create_t_m_s.tmQtyKarung','create_t_m_s.tmQtyKg','create_t_m_s.tglMuat','products.itemName','customers.custName','createsppbs.sppbNo','create_t_m_s.isSecCek')->whereNull('isSpm')->whereNotNull('create_t_m_s.isSecCek')->get();
        // dd($getTm);
        return view('livewire.createspm', ['datatm' => $getTm, 'dataspm' => $data, 'transporter' => $angkutan,'product' => $barang,'listsppb' => $getsppb,'antrian' => $tiket,'kemasan' => $paking, 'jenistruk' => $truk]);
    }
}
