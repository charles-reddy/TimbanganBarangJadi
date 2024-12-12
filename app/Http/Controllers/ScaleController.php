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
        $tgl1 = Carbon::now();
        $tglnow=date('d-m-Y H:i',strtotime($tgl1));
        $tgl=date('Y-m-d',strtotime($tgl1));
        $strukout =  DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('id',$id)->get();
        // dd($strukout);
            return view('cetakout1', compact('strukout'));
    //    $html = view('cetakout1', compact('strukout'));

    //     $mpdf = new \Mpdf\Mpdf();
    //     $mpdf->WriteHTML($html);
    //     $mpdf->Output();
    }

    public function cetakspm($id)
    {
        $tgl1 = Carbon::now();
        $tglnow=date('d-m-Y H:i',strtotime($tgl1));
        $tgl=date('Y-m-d',strtotime($tgl1));
        $strukspm =  DB::connection('sqlsrv')->table('createspms')->join('transporters', 'transporters.transpID', 'createspms.transpID')->join('products', 'products.itemCode', 'createspms.itemCode')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->where('createspms.id',$id)->get();
        //   dd($strukspm);
            return view('cetakspm', compact('strukspm'));
    //    $html = view('cetakout1', compact('strukout'));

    //     $mpdf = new \Mpdf\Mpdf();
    //     $mpdf->WriteHTML($html);
    //     $mpdf->Output();
    }


    public function cetaksj($id)
    {
        $tgl1 = Carbon::now();
        $tglnow=date('d-m-Y H:i',strtotime($tgl1));
        $tgl=date('Y-m-d',strtotime($tgl1));
        $struksj =  DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->join('createspms', 'createspms.id', 'trscale.spmID')->join('createsppbs', 'createsppbs.id', 'trscale.doNo')->where('trscale.id',$id)->get();
        //   dd($struksj);
            return view('cetaksj', compact('struksj'));
    //    $html = view('cetakout1', compact('strukout'));

    //     $mpdf = new \Mpdf\Mpdf();
    //     $mpdf->WriteHTML($html);
    //     $mpdf->Output();
    }


    


    
}
