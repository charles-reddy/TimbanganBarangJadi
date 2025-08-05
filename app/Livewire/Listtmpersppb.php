<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Listtmpersppb extends Component
{
    public $noSppb;
    public $katakunci;
    use WithPagination;

    public function edit($id) 
    {
        $this->noSppb = $id;
        // dd($this->noSppb);
       
    }

   

    public function render()
    {   
        if($this->noSppb) {
            // dd('atas');
            //  $sdhtimbang =  DB::connection('sqlsrv')->table('create_t_m_s')->join('createsppbs', 'createsppbs.id','create_t_m_s.tmSppbID')->join('createspms','createspms.sppbNo', 'createsppbs.id')->join('trscale','trscale.spmID','createspms.id')->where('createsppbs.id','=', $this->noSppb)->where('isspm','1')->select('create_t_m_s.id', 'create_t_m_s.pendfNo','trscale.netto', 'createsppbs.sppbQtyKg', 'createsppbs.sppbNo')->paginate(100);
             $sdhtimbang =  DB::connection('sqlsrv')->table('create_t_m_s')->join('createspms','createspms.tiketID','create_t_m_s.id')->join('createsppbs', 'createsppbs.id','create_t_m_s.tmSppbID')->join('trscale','trscale.spmID','createspms.id')->join('products','products.itemCode', 'createspms.itemCode')->where('createsppbs.id','=', $this->noSppb)->where('isspm','1')->select('create_t_m_s.id', 'create_t_m_s.pendfNo','trscale.netto','trscale.jam_out', 'createsppbs.sppbQtyKg', 'createsppbs.sppbNo', 'create_t_m_s.tmQtyKg','products.type','createspms.spmNo')->orderBy('create_t_m_s.id','desc')->paginate(100);
            $countsdhtimbang =  DB::connection('sqlsrv')->table('create_t_m_s')->join('createspms','createspms.tiketID','create_t_m_s.id')->join('createsppbs', 'createsppbs.id','create_t_m_s.tmSppbID')->join('trscale','trscale.spmID','createspms.id')->join('products','products.itemCode', 'createspms.itemCode')->where('createsppbs.id','=', $this->noSppb)->where('isspm','1')->select('create_t_m_s.id', 'create_t_m_s.pendfNo','trscale.netto', 'createsppbs.sppbQtyKg', 'createsppbs.sppbNo', 'create_t_m_s.tmQtyKg','products.type','createspms.spmNo')->orderBy('create_t_m_s.id','desc')->exists();
            
            // $tidakdatang =  DB::connection('sqlsrv')->table('create_t_m_s')->join('createspms','createspms.tiketID','create_t_m_s.id')->join('createsppbs', 'createsppbs.id','create_t_m_s.tmSppbID')->where('createsppbs.id','=', $this->noSppb)->whereNull('isspm')->select('create_t_m_s.id', 'create_t_m_s.pendfNo', 'create_t_m_s.tmQtyKg', 'createsppbs.sppbNo')->paginate(100);
            $tidakdatang =  DB::connection('sqlsrv')->table('create_t_m_s')->join('createsppbs', 'createsppbs.id','create_t_m_s.tmSppbID')->leftJoin('createspms','createspms.tiketID','create_t_m_s.id')->where('createsppbs.id','=', $this->noSppb)->whereNull('isspm')->orwhere('createsppbs.id','=', $this->noSppb)->Where('createspms.isIN', false)->select('create_t_m_s.id', 'create_t_m_s.pendfNo', 'create_t_m_s.tmQtyKg', 'create_t_m_s.tglMuat', 'createsppbs.sppbNo')->paginate(100);
            // dd($tidakdatang, $this->noSppb);
        // $sdhtimbangtotal =  DB::connection('sqlsrv')->table('create_t_m_s')->join('createspms','createspms.tiketID','create_t_m_s.id')->join('createsppbs', 'createsppbs.id','create_t_m_s.tmSppbID')->join('trscale','trscale.spmID','createspms.id')->where('createsppbs.id','=', $this->noSppb)->where('isspm',1)->sum('trscale.netto');
            $sdhtimbangtotal = 0;
            $sdhtimbangtotalliq = 0;
           
            if($countsdhtimbang) {
                foreach( $sdhtimbang as $value){
                $sdhtimbangtotalliq += $value->netto;
                $sdhtimbangtotal += $value->tmQtyKg;
                $fgtype = $value->type;
                }
            } else {
                $fgtype = 'FG';
            }
                
            

        // $tidakdatangtotal =  DB::connection('sqlsrv')->table('create_t_m_s')->join('createspms','createspms.tiketID','create_t_m_s.id')->join('createsppbs', 'createsppbs.id','create_t_m_s.tmSppbID')->where('createsppbs.id','=', $this->noSppb)->whereNull('isspm')->sum('create_t_m_s.tmQtyKg');
            $tidakdatangtotal = 0;
            foreach($tidakdatang as $key){
                $tidakdatangtotal += $key->tmQtyKg;
            }
            
            if($this->katakunci  !=null){
                $data =   DB::connection('sqlsrv')->table('createsppbs')->join('customers','customers.custID', 'createsppbs.custID')->where('sppbNo','like' ,'%' . $this->katakunci . '%')->orderBy('id')->paginate(10);
            } else {
                $data =   DB::connection('sqlsrv')->table('createsppbs')->join('customers','customers.custID', 'createsppbs.custID')->orderBy('id')->paginate(10);
            }
        // dd($sdhtimbang, $tidakdatang, $sdhtimbangtotal, $tidakdatangtotal,$sdhtimbangtotalliq, $fgtype);
        } else {
            // dd('bawah');
            if($this->katakunci  !=null){
                $data =   DB::connection('sqlsrv')->table('createsppbs')->join('customers','customers.custID', 'createsppbs.custID')->where('sppbNo','like' ,'%' . $this->katakunci . '%')->orderBy('id')->paginate(10);
            } else {
                $data =   DB::connection('sqlsrv')->table('createsppbs')->join('customers','customers.custID', 'createsppbs.custID')->orderBy('id')->paginate(10);
            }   
            // $data =   DB::connection('sqlsrv')->table('createsppbs')->join('customers','customers.custID', 'createsppbs.custID')->orderBy('id')->paginate(10);
            $sdhtimbang =  DB::connection('sqlsrv')->table('create_t_m_s')->where('id','-1')->paginate(1);
            $tidakdatang =  DB::connection('sqlsrv')->table('create_t_m_s')->where('id','-1')->paginate(1);
            $sdhtimbangtotal = 0;
            $sdhtimbangtotalliq = 0;
            $tidakdatangtotal = 0;
            $fgtype = 'FG';
        }
        
        // dd($data);
        return view('livewire.listtmpersppb',['datasppb' => $data, 'sdhtimbang' => $sdhtimbang, 'tidakdatang' => $tidakdatang, 'sdhtimbangtotal' => $sdhtimbangtotal , 'tidakdatangtotal' => $tidakdatangtotal,'sdhtimbangtotalliq' => $sdhtimbangtotalliq, 'fgtype'=> $fgtype ]);
    }
}
