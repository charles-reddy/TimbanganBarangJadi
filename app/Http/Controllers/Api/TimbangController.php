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
        $spmlist = DB::connection('sqlsrv')->table('createspms')->join('customers', 'customers.custID', 'createspms.custID')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID  ')->select('createspms.id', 'createspms.spmNo', 'createspms.carID', 'createspms.driver', 'customers.custName', 'create_t_m_s.pendfNo', 'create_t_m_s.tglMuat')->where('isIN', 0)->whereNull('eksesMol')->whereDate('tglSpm', '>', Carbon::now()->addDays(-5))->orderBy('id', 'desc')->get();
        return response()->json([
            'status' => true,
            'message' => 'Data BERHASIL Dimuat',
            'data' => $spmlist
        ], 200);
    }
}
