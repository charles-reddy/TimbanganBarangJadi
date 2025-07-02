<?php

namespace App\Livewire;

use App\Models\suppCustomer;
use App\Models\Product;
use App\Models\supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;

class Registrasimaterial extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';
    public $sortColumn = 'jam_reg';
    public $sortDirection = 'desc';
    #[Validate('required', message: 'isi Nama Sopir')]
    public $driver;
    #[Validate('required', message: 'isi no Kendaraan')]
    public $carID;
    #[Validate('required', message: 'isi Supplier')]
    public $suppID;
    public $suppID1;
    public $jam_in;
    public $katakunci;
    public $jam_reg;
    public $userIDIN;
    public $usernameIN;
    public $userIDREG;
    public $usernameREG;
    #[Validate('required', message: 'isi kode barang')]
    public $itemCode;
    public $itemCode1;
    public $doNo;
    public $poNo;
    public $remarks;
    public $isDisabled = false;
    public $transID;
    public $updateData = false;
    

    


    public function store()
    {
        $jam_reg = Carbon::now();
        $userIDREG = Auth::user()->id;
        $usernameREG = Auth::user()->username;
        $cid = explode("-",$this->suppID);
       
        $this->validate();

        try {
                $this->jam_reg = $jam_reg;
                $this->userIDREG = $userIDREG; 
                $this->usernameREG = $usernameREG;
                $this->suppID = $cid[0];
                

            DB::connection('sqlsrv')->table('trscaleb19s')->insert([
                'driver' => $this->driver,
                'carID' => $this->carID,
                'suppID' => $this->suppID,
                'jam_reg' => $this->jam_reg,
                'userIDREG' => $this->userIDREG,
                'usernameREG' => $this->usernameREG,
                'created_at' => $this->jam_reg,
                'itemCode' => $this->itemCode,
                'doNo' => $this->doNo,
                'poNo' => $this->poNo,
                'remarks' => $this->remarks,
            ]);

                session()->flash('message', 'Data berhasil dimasukkan');
                $this->clear();
                redirect('/registrasimaterial');
        } catch (Exception $e) {
            throw $e;
            // session()->flash('error', 'failed to store data');
            return;
        }

    }


    public function edit($id)
    {
        
        $data = DB::connection('sqlsrv')->table('trscaleb19s')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->join('products', 'products.itemCode', 'trscaleb19s.itemCode')->where('trscaleb19s.id',$id)->first();
        // dd($data);
        $this->driver = $data->driver;
        $this->carID = $data->carID;
        $this->poNo = $data->poNo;
        $this->doNo = $data->doNo;
        $this->remarks = $data->remarks;
        $this->isDisabled = true;
        $this->suppID1 = $data->suppName;
        $this->itemCode1 = $data->itemName;
        $this->transID = $id;
        $this->updateData = true;
        
    }

    public function update()
    {
        $jam_reg = Carbon::now();
        $userIDREG = Auth::user()->id;
        $usernameREG = Auth::user()->username;
        $cid = explode("-",$this->suppID);
        $this->validate([
            'driver' => 'required',
            'carID' => 'required',
        ]);
        // $this->validateOnly($propertyName, [
        //     'driver' => 'required',
        //     'carID' => 'required',
        // ]);

        try {
            $this->jam_reg = $jam_reg;
            $this->userIDREG = $userIDREG; 
            $this->usernameREG = $usernameREG;
            $this->suppID = $cid[0];
            
            if ($this->suppID) {
                DB::connection('sqlsrv')->table('trscaleb19s')->where('id',$this->transID)->update([
                    'driver' => $this->driver,
                    'carID' => $this->carID,
                    'suppID' => $this->suppID,
                    'jam_reg' => $this->jam_reg,
                    'userIDREG' => $this->userIDREG,
                    'usernameREG' => $this->usernameREG,
                    'updated_at' => $this->jam_reg,
                    'doNo' => $this->doNo,
                    'poNo' => $this->poNo,
                    'remarks' => $this->remarks,
                ]);
            } else if ($this->itemCode) {
                DB::connection('sqlsrv')->table('trscaleb19s')->where('id',$this->transID)->update([
                    'driver' => $this->driver,
                    'carID' => $this->carID,
                    'jam_reg' => $this->jam_reg,
                    'userIDREG' => $this->userIDREG,
                    'usernameREG' => $this->usernameREG,
                    'updated_at' => $this->jam_reg,
                    'itemCode' => $this->itemCode,
                    'doNo' => $this->doNo,
                    'poNo' => $this->poNo,
                    'remarks' => $this->remarks,
                ]);
            } else if ($this->itemCode and $this->suppID) {
                DB::connection('sqlsrv')->table('trscaleb19s')->where('id',$this->transID)->update([
                    'driver' => $this->driver,
                    'carID' => $this->carID,
                    'suppID' => $this->suppID,
                    'jam_reg' => $this->jam_reg,
                    'userIDREG' => $this->userIDREG,
                    'usernameREG' => $this->usernameREG,
                    'updated_at' => $this->jam_reg,
                    'itemCode' => $this->itemCode,
                    'doNo' => $this->doNo,
                    'poNo' => $this->poNo,
                    'remarks' => $this->remarks,
                ]);
            } else {
                DB::connection('sqlsrv')->table('trscaleb19s')->where('id',$this->transID)->update([
                    'driver' => $this->driver,
                    'carID' => $this->carID,
                    'jam_reg' => $this->jam_reg,
                    'userIDREG' => $this->userIDREG,
                    'usernameREG' => $this->usernameREG,
                    'updated_at' => $this->jam_reg,
                    'doNo' => $this->doNo,
                    'poNo' => $this->poNo,
                    'remarks' => $this->remarks,
                ]);
            }
            

            

                session()->flash('message', 'Data berhasil dimasukkan');
                $this->clear();
                redirect('/registrasimaterial');
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
       $this->suppID ='';
       $this->doNo ='';
       $this->poNo ='';
       $this->remarks ='';
        
       
        
        redirect('/registrasimaterial');
    }


    public function render()
    {
        // dd($this->isDisabled);
        $pemasok = supplier::all();
        $barang = Product::get();
        if ($this->katakunci !=null) {
            $data = DB::connection('sqlsrv')->table('trscaleb19s')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->join('products', 'products.itemCode', 'trscaleb19s.itemCode')->where('suppName','like','%' . $this->katakunci . '%')->orwhere('carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        } else {
            $data = DB::connection('sqlsrv')->table('trscaleb19s')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->join('products', 'products.itemCode', 'trscaleb19s.itemCode')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        }
        return view('livewire.registrasimaterial', ['datascale' => $data, 'supplier' => $pemasok, 'product' => $barang]); 
    }
}
