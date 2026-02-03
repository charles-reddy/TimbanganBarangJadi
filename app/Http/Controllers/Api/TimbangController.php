<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimbangController extends Controller
{
    public function index()
    {
        $spmlist = DB::connection('sqlsrv')->table('createspms')->join('customers', 'customers.custID', 'createspms.custID')->join('products','products.itemCode','createspms.itemCode')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID  ')->join('createsppbs','createsppbs.id','createspms.sppbNo')->select('createspms.id', 'createspms.spmNo', 'createspms.carID', 'createspms.driver', 'customers.custName', 'create_t_m_s.pendfNo', 'create_t_m_s.tglMuat', 'customers.custID', 'createspms.itemCode', 'createspms.dnNo', 'createsppbs.poNo','products.itemName')->where('isIN', 0)->whereNull('eksesMol')->whereDate('tglSpm', '>', Carbon::now()->addDays(-5))->orderBy('id', 'desc')->get();
        return response()->json([
            'status' => true,
            'message' => 'Data BERHASIL Dimuat',
            'data' => $spmlist
        ], 200);
    }

    public function timbangin(Request $request)
    {
        
        DB::connection('sqlsrv')->table('trscale')->insert([
            'timbangin' => $request->timbangin,
            'spmID' => $request->spmID,
            'created_at' => Carbon::now(),
            'carID' => $request->carID,
            'driver' => $request->driver,
            'custID' => $request->custID,
            'itemCode' => $request->itemCode,
            'doNo' => $request->doNo,
            'poNo' => $request->poNo,
            'userIDIN' => $request->userIDIN,
            'usernameIN' => $request->usernameIN,
            'jam_in' => Carbon::now(),
            'isLoadingDone' => True,

        ]);

        DB::connection('sqlsrv')->table('createspms')->where('id', $request->spmID)->update([
            'isIN' => true,

        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data BERHASIL Diupdate',
        ], 200);
    }


    public function siapKeluar()
    {
        
        $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->whereDate('jam_in','>', Carbon::now()->addDays(-5) )->wherenull('netto')->whereNotNull('isLoadingDone')->get();

        return response()->json([
            'status' => true,
            'message' => 'Data BERHASIL dimuat',
            'data' => $data
        ], 200);
    }


    public function timbangout(Request $request)
    {
        
        DB::connection('sqlsrv')->table('trscale')->where('id', $request->timbangID)->update([
            'timbangout' => $request->timbangout,
            'userIDOUT' => $request->userIDOUT,
            'usernameOUT' => $request->usernameOUT,
            'jam_out' => Carbon::now(),
            'netto' => $request->netto,
            'updated_at' => Carbon::now(),
            'avgKarung' => $request->avgKarung,
            'b10QtyKarung' => $request->b10QtyKarung,



        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data BERHASIL Diupdate',
        ], 200);
    }

}
