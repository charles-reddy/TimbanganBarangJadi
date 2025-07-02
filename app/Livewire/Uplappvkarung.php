<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\JembatanTimbang;
use App\Models\Product;
use App\Models\Transporter;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class Uplappvkarung extends Component
{
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';
    public $sortColumn = 'jam_in';
    public $sortDirection = 'desc';
    public $driver;
    public $carID;
    public $doNo;
    public $poNo;
    public $remarks;
    public $custID;
    public $spmID;
    public $spmNo;
    public $transpID;
    public $itemCode;
    public $updateData = false;
    public $id_trscale;
    public $katakunci;
    public $katakunciout;
    public $trscaleSelectedID = [];
    public $selected ='';
    public $custName;
    public $transpName;
    public $itemName;
    public $jembatanTimbang;
    public $timbangin;
    public $timbanganID;
    public $timbangout;
    public $transID;
    public $netto;
    public $timbanganoutID;
    public $custN;
    public $jam_out;
    public $userIDOUT;
    public $usernameOUT;
    #[Validate('required', message: 'qty Karung harus diisi')]
    #[Validate('integer', message: 'qty Karung harus dalam angka')]
    public $b10QtyKarung;
    public $avgKarung;
    #[Rule('max:1024', message: 'Foto Bukti 1 maks 1 MB')]
    #[Validate('required', message: 'Silahkan Upload foto Bukti 1')]
    #[Validate('image', message: 'Bukti 1 harus image')]
    public $buktiAppKarung1;
    #[Validate('required', message: 'Silahkan Upload foto Bukti 2')]
    #[Rule('max:1024', message: 'Foto Bukti 2 maks 1 MB')]
    #[Validate('image', message: 'Bukti 2 harus image')]
    public $buktiAppKarung2;
    #[Rule('max:1024', message: 'Foto Bukti 3 maks 1 MB')]
    #[Rule('max:1024', message: 'Foto Bukti 3 maks 1 MB')]
    #[Validate('image', message: 'Bukti 3 harus image')]
    public $buktiAppKarung3;
    
    public function render()
    {
        if (($this->katakunci or $this->katakunciout)  !=null) {
            // $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunci . '%')->where('isApp','<>', 1)->wherenull('netto')->where('avgKarung','<', 50.01)->orwhere('avgKarung','>', 50.25)->wherenull('b10QtyKarung')->orwhere('carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunci . '%')->where('isApp','<>', 1)->wherenull('netto')->where('avgKarung','<', 50.01)->orwhere('avgKarung','>', 50.25)->wherenull('b10QtyKarung')->orwhere('carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
       
        } else {
            // $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('isApp',false)->wherenull('netto')->WhereNotBetween('avgKarung',[50.01, 50.25])->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('isApp',false)->wherenull('netto')->WhereNotBetween('avgKarung',[50.01, 50.25])->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        
        }
        $timbangan = JembatanTimbang::all();
        $pelanggan = Customer::all();
        $angkutan = Transporter::all();
        $barang = Product::all();
        
        return view('livewire.uplappvkarung', ['datascale' => $data, 'customer' => $pelanggan, 'transporter' => $angkutan, 'product' => $barang, 'timbangan' => $timbangan]);
    }
}
