<?php

namespace App\Http\Controllers;

use App\Exports\ExportTimbangOut;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ScaleController extends Controller
{
    public function cetakout($id)
    {
        $mpdf = new \Mpdf\Mpdf();
        $tgl1 = Carbon::now();
        $tglnow=date('d-m-Y H:i',strtotime($tgl1));
        $tgl=date('Y-m-d',strtotime($tgl1));
        // $strukout =  DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('id',$id)->get();
         $strukout =  DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->join('createspms', 'createspms.id', 'trscale.spmID')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->select('trscale.id','trscale.driver','trscale.carID','trscale.custID','trscale.transpID','trscale.itemCode','products.itemName','trscale.doNo','trscale.poNo','trscale.remarks','trscale.timbangout','trscale.netto','trscale.timbangin','trscale.timbanganID','trscale.timbanganoutID','trscale.grossBeforeDed','trscale.jam_in','trscale.jam_out','trscale.userIDIN','trscale.userIDOUT','trscale.usernameOUT','trscale.spmID','trscale.b10QtyKarung','trscale.b10BatchNo','trscale.avgKarung','trscale.isApp','trscale.isAppID','trscale.isAppDate','customers.custName','customers.custAdd','products.deduction','products.type','products.uom','createspms.spmNo','createsppbs.sppbNo','createspms.dnNo','createspms.sealNo','createspms.kontainerNo')->where('trscale.id',$id)->get();
       
        // dd($strukout);
            // return view('cetakout1', compact('strukout'));
       $html = view('cetakout1', compact('strukout'));

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function cetakoutm($id)
    {
        $mpdf = new \Mpdf\Mpdf();
        $tgl1 = Carbon::now();
        $tglnow=date('d-m-Y H:i',strtotime($tgl1));
        $tgl=date('Y-m-d',strtotime($tgl1));
        $strukout =  DB::connection('sqlsrv')->table('trscaleb19s')->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')->join('products', 'products.itemCode', 'trscaleb19s.itemCode')->where('id',$id)->get();
        // dd($strukout);
            // return view('cetakout1', compact('strukout'));
       $html = view('cetakoutm1', compact('strukout'));

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function cetakspm($id)
    {
        
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [90, 36]]);
        $tgl1 = Carbon::now();
        $tglnow=date('d-m-Y H:i',strtotime($tgl1));
        $tgl=date('Y-m-d',strtotime($tgl1));
        $strukspm =  DB::connection('sqlsrv')->table('createspms')->join('customers', 'customers.custID', 'createspms.custID')->join('products', 'products.itemCode', 'createspms.itemCode')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->where('createspms.id',$id)->get();
        //   dd($strukspm);
            // return view('cetakspm', compact('strukspm'));
       $html = view('cetakspm', compact('strukspm'));

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function cetaksegel($id)
    {
        
        $mpdf = new \Mpdf\Mpdf();
        $tgl1 = Carbon::now();
        $tglnow=date('d-m-Y H:i',strtotime($tgl1));
        $tgl=date('Y-m-d',strtotime($tgl1));
        $struksegel =  DB::connection('sqlsrv')->table('createspms')->join('customers', 'customers.custID', 'createspms.custID')->join('products', 'products.itemCode', 'createspms.itemCode')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('trscale', 'trscale.spmID', 'createspms.id')->where('createspms.id',$id)->get();
        // $logtimbavg =  DB::connection('sqlsrv')->table('logAppAvgKarung')->where('trscaleID',)->get(); 
        // dd($struksegel);
            // return view('cetaksegel', compact('struksegel'));
            // $mpdf->Image('storage/uploads/segel/SPM-7-5-2025-1.jpg', 0, 0, 210, 297, 'jpg', '', true, false);
            // $mpdf->Image('storage/uploads/segel/SPM-5-4-2025-2.jpg', 0, 0, 210, 297, 'jpg', '', true, false);
       $html = view('cetaksegel', compact('struksegel'));

        $mpdf = new \Mpdf\Mpdf();
       
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }


    public function cetaksj($id)
    {
        // dd($id);
        $mpdf = new \Mpdf\Mpdf();
        $tgl1 = Carbon::now();
        $tglnow=date('d-m-Y H:i',strtotime($tgl1));
        $tgl=date('Y-m-d',strtotime($tgl1));
        // $struksj =  DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->join('createspms', 'createspms.id', 'trscale.spmID')->join('createsppbs', 'createsppbs.id', 'trscale.doNo')->where('trscale.id',$id)->get();
        $struksj =  DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('createspms', 'createspms.id', 'trscale.spmID')->join('products', 'products.itemCode', 'trscale.itemCode')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->where('trscale.id',$id)->get();
       
        //  dd($struksj);
            // return view('cetaksj', compact('struksj'));
       $html = view('cetaksj', compact('struksj'));

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }


    public function cetaktiket($id)
    {
        $mpdf = new \Mpdf\Mpdf();
        $tgl1 = Carbon::now();
        $tglnow=date('d-m-Y H:i',strtotime($tgl1));
        $tgl=date('Y-m-d',strtotime($tgl1));
        $struktiketmuat =  DB::connection('sqlsrv')->table('create_t_m_s')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->join('jenistruks','jenistruks.id', 'create_t_m_s.jenisTruk')->join('customers','customers.custID', 'create_t_m_s.custID')->join('products','products.itemCode', 'create_t_m_s.itemCode')->select('create_t_m_s.id','pendfNo', 'tmQtyKg','tmQtyKarung','sppbNo','tmCarID','isMktApp','tmDriver','noHpDriver','jenistruks.jenisTruk','tglDaftar','tmTranspName','tglMuat','custName', 'itemName')->where('create_t_m_s.id',$id)->get();
        // dd($struktiketmuat);
        // return view('cetakstrukmuat', compact('struktiketmuat', 'tglnow'));
        $html = view('cetakstrukmuat', compact('struktiketmuat', 'tglnow'));
        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 23,
            'default_font' => 'arial'
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
    


    
}
