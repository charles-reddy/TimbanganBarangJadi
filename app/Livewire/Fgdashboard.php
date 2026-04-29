<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Fgdashboard extends Component
{
    public $transac;
    public $jmltruk;

    public function mount()
    {
        // $transac = DB::connection('sqlsrv')->table('trscale')->whereNot('jam_out')->orderBy('jam_out','desc')
        //     ->select(db::raw("sum(netto) as netto"), db::raw(date('d-m-Y',strtotime('jam_out'))), db::raw("id") )
        //     ->groupBy('jam_out')
        //     ->get();

        $transac = DB::connection('sqlsrv')->table('vwSummaryTruckFG')->orderBy('tgl', 'desc')->Limit(7)->get();
        // dd($transac);
        foreach ($transac as $item) {
            $data['label'][] =  $item->tgl;
            $data['data'][] = (int) $item->totalNetto;
        }

        $this->transac =  json_encode($data);
        // dd($this->transac);

        $jmltruk = DB::connection('sqlsrv')->table('vwSummaryTruckFG')->orderBy('tgl', 'desc')->Limit(7)->get();
        // dd($transac);
        foreach ($jmltruk as $item) {
            $data1['label'][] =  $item->tgl;
            $data1['data'][] = (int) $item->totalTruk;
        }

        $this->jmltruk =  json_encode($data1);
        // dd($this->jmltruk);
    }

    public function render()
    {
        // dd(date('d-m-Y',strtotime(Carbon::now()->addDays(-1))));
        $antrianskr = DB::connection('sqlsrv')->table('vwTiketMuat')->whereDate('tgl', '=', Carbon::now())->orderBy('tgl', 'desc')->select('antrian')->first();
        $antrianbsk = DB::connection('sqlsrv')->table('vwTiketMuat')->whereDate('tgl', '=', Carbon::now()->addDays(+1))->orderBy('tgl', 'desc')->select('antrian')->first();
        // dd($antrianskr, $antrianbsk);
        // $registrasi = DB::connection('sqlsrv')->table('createspms')->whereDate('tglSpm','=', Carbon::now() )->where('isIN','=',0)->count('id');
        // $registrasi = DB::connection('sqlsrv')->table('createspms')->whereDate('tglSpm','=', Carbon::now() )->count('id');
        $registrasi = DB::connection('sqlsrv')->table('create_t_m_s')->whereDate('tglMuat', '=', date('Y-m-d', strtotime(Carbon::now())))->wherenotnull('isSecCek')->whereNotNull('isSPM')->count('id');
        // dd($registrasi);
        $registrasikmrblmmasuk = DB::connection('sqlsrv')->table('createspms')->whereDate('tglSpm', '=', Carbon::now()->addDays(-1))->where('isIN', '=', 0)->count('id');
        $timbanginkmrblmkeluar = DB::connection('sqlsrv')->table('trscale')->whereDate('created_at', '=', Carbon::now()->addDays(-1))->wherenull('timbangout')->count('id');
        $tidakdatang = DB::connection('sqlsrv')->table('create_t_m_s')->whereDate('tglMuat', '=', date('Y-m-d', strtotime(Carbon::now()->addDays(-1))))->wherenull('isSecCek')->count('id');
        $tmsdhmasuk = DB::connection('sqlsrv')->table('create_t_m_s')->whereDate('tglMuat', '=', date('Y-m-d', strtotime(Carbon::now())))->wherenotnull('isSecCek')->count('id');
        $pendingkmr = $timbanginkmrblmkeluar + $registrasikmrblmmasuk + $tidakdatang;
        // dd($tmsdhmasuk);
        $data = DB::connection('sqlsrv')->table('vwSummaryTruckFG')->orderBy('tgl', 'desc')->first();
        $data7hari = DB::connection('sqlsrv')->table('create_t_m_s')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->join('jenistruks', 'jenistruks.id', 'create_t_m_s.jenisTruk')->whereBetween('tglMuat', [Carbon::now(), Carbon::now()->addDays(+7)])->where('create_t_m_s.tmQtyKg', '>', 0)->orderBy('tglMuat', 'asc')->paginate(10);
        // dd($data7hari);

        // Query data per shift hari ini
        // Shift 1: 08:00 - 12:00
        $shift1 = DB::connection('sqlsrv')->table('trscale')
            ->join('createspms', 'createspms.id', 'trscale.spmID')
            ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
            ->whereNotNull('netto')
            ->where('create_t_m_s.tmQtyKg', '>', 0)
            ->whereDate('jam_out', Carbon::now())
            ->whereTime('jam_out', '>=', '08:00:00')
            ->whereTime('jam_out', '<', '12:00:00')
            ->selectRaw('COUNT(trscale.id) as totalTruk, ISNULL(SUM(netto), 0) as totalNetto')
            ->first();

        // Shift 2: 12:00 - 16:00
        $shift2 = DB::connection('sqlsrv')->table('trscale')
            ->join('createspms', 'createspms.id', 'trscale.spmID')
            ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
            ->whereNotNull('netto')
            ->where('create_t_m_s.tmQtyKg', '>', 0)
            ->whereDate('jam_out', Carbon::now())
            ->whereTime('jam_out', '>=', '12:00:00')
            ->whereTime('jam_out', '<', '16:00:00')
            ->selectRaw('COUNT(trscale.id) as totalTruk, ISNULL(SUM(netto), 0) as totalNetto')
            ->first();

        // Shift 3: 16:00 - 20:00
        $shift3 = DB::connection('sqlsrv')->table('trscale')
            ->join('createspms', 'createspms.id', 'trscale.spmID')
            ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
            ->whereNotNull('netto')
            ->where('create_t_m_s.tmQtyKg', '>', 0)
            ->whereDate('jam_out', Carbon::now())
            ->whereTime('jam_out', '>=', '16:00:00')
            ->whereTime('jam_out', '<', '20:00:00')
            ->selectRaw('COUNT(trscale.id) as totalTruk, ISNULL(SUM(netto), 0) as totalNetto')
            ->first();

        // Outside Shift: Sebelum 08:00 atau setelah 20:00
        $shiftOutside = DB::connection('sqlsrv')->table('trscale')
            ->join('createspms', 'createspms.id', 'trscale.spmID')
            ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
            ->whereNotNull('netto')
            ->where('create_t_m_s.tmQtyKg', '>', 0)
            ->whereDate('jam_out', Carbon::now())
            ->where(function ($query) {
                $query->whereTime('jam_out', '<', '08:00:00')
                    ->orWhereTime('jam_out', '>=', '20:00:00');
            })
            ->selectRaw('COUNT(trscale.id) as totalTruk, ISNULL(SUM(netto), 0) as totalNetto')
            ->first();

        // Get quota hari ini
        $quotaToday = DB::connection('sqlsrv')->table('tbl_QuotaLoading')
            ->whereDate('quotaTglDatang', Carbon::now())
            ->where('isApprove', true)
            ->first();

        // Jika tidak ada, ambil quota default (quotaTglDatang = NULL)
        if (!$quotaToday) {
            $quotaToday = DB::connection('sqlsrv')->table('tbl_QuotaLoading')
                ->whereNull('quotaTglDatang')
                ->where('isApprove', true)
                ->orderBy('id', 'desc')
                ->first();
        }

        // Query 7 hari kerja terakhir (exclude Minggu) untuk chart shift performance
        $last7WorkingDays = [];
        $currentDate = Carbon::now();
        $daysAdded = 0;
        $dayOffset = 0;

        while ($daysAdded < 7) {
            $checkDate = $currentDate->copy()->subDays($dayOffset);
            // 0 = Minggu (Sunday), skip jika Minggu
            if ($checkDate->dayOfWeek != 0) {
                $last7WorkingDays[] = $checkDate->format('Y-m-d');
                $daysAdded++;
            }
            $dayOffset++;
        }
        $last7WorkingDays = array_reverse($last7WorkingDays);

        // Query data per shift untuk 7 hari kerja terakhir
        $shiftPerformance = DB::connection('sqlsrv')->table('trscale')
            ->join('createspms', 'createspms.id', 'trscale.spmID')
            ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
            ->whereNotNull('netto')
            ->where('create_t_m_s.tmQtyKg', '>', 0)
            ->whereIn(DB::raw('CAST(jam_out as DATE)'), $last7WorkingDays)
            ->selectRaw("
                CAST(jam_out as DATE) as tanggal,
                CASE 
                    WHEN CAST(jam_out as TIME) >= '08:00' AND CAST(jam_out as TIME) < '12:00' THEN 'Shift 1'
                    WHEN CAST(jam_out as TIME) >= '12:00' AND CAST(jam_out as TIME) < '16:00' THEN 'Shift 2'
                    WHEN CAST(jam_out as TIME) >= '16:00' AND CAST(jam_out as TIME) < '20:00' THEN 'Shift 3'
                    ELSE 'Outside'
                END as shift,
                COUNT(trscale.id) as totalTruk,
                ISNULL(SUM(netto), 0) as totalNetto
            ")
            ->groupBy(DB::raw('CAST(jam_out as DATE)'))
            ->groupBy(DB::raw("
                CASE 
                    WHEN CAST(jam_out as TIME) >= '08:00' AND CAST(jam_out as TIME) < '12:00' THEN 'Shift 1'
                    WHEN CAST(jam_out as TIME) >= '12:00' AND CAST(jam_out as TIME) < '16:00' THEN 'Shift 2'
                    WHEN CAST(jam_out as TIME) >= '16:00' AND CAST(jam_out as TIME) < '20:00' THEN 'Shift 3'
                    ELSE 'Outside'
                END
            "))
            ->orderBy('tanggal', 'asc')
            ->get();

        // Format data untuk Chart.js
        $chartLabels = [];
        $shift1Data = [];
        $shift2Data = [];
        $shift3Data = [];
        $outsideShiftData = [];

        foreach ($last7WorkingDays as $date) {
            $dateObj = Carbon::parse($date);
            // Format: Sen 25-03
            $dayName = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            $chartLabels[] = $dayName[$dateObj->dayOfWeek] . ' ' . $dateObj->format('d-m');

            // Get data per shift untuk tanggal ini
            $s1 = $shiftPerformance->where('tanggal', $date)->where('shift', 'Shift 1')->first();
            $s2 = $shiftPerformance->where('tanggal', $date)->where('shift', 'Shift 2')->first();
            $s3 = $shiftPerformance->where('tanggal', $date)->where('shift', 'Shift 3')->first();
            $sOutside = $shiftPerformance->where('tanggal', $date)->where('shift', 'Outside')->first();

            $shift1Data[] = $s1 ? $s1->totalTruk : 0;
            $shift2Data[] = $s2 ? $s2->totalTruk : 0;
            $shift3Data[] = $s3 ? $s3->totalTruk : 0;
            $outsideShiftData[] = $sOutside ? $sOutside->totalTruk : 0;
        }

        $shiftChartData = json_encode([
            'labels' => $chartLabels,
            'shift1' => $shift1Data,
            'shift2' => $shift2Data,
            'shift3' => $shift3Data,
            'outsideShift' => $outsideShiftData,
        ]);

        // Query delivery details dengan kolom shift
        $dataout = DB::connection('sqlsrv')->table('trscale')
            ->join('customers', 'customers.custID', 'trscale.custID')
            ->join('products', 'products.itemCode', 'trscale.itemCode')
            ->join('createspms', 'createspms.id', 'trscale.spmID')
            ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
            ->whereNotNull('netto')
            ->where('create_t_m_s.tmQtyKg', '>', 0)
            ->whereDate('jam_out', Carbon::now())
            ->selectRaw(
                "trscale.*, customers.custName, products.itemName, createspms.driver, createspms.carID, createspms.spmNo, create_t_m_s.pendfNo, 
                CASE 
                    WHEN CAST(jam_out as TIME) >= '08:00' AND CAST(jam_out as TIME) < '12:00' THEN 'Shift 1'
                    WHEN CAST(jam_out as TIME) >= '12:00' AND CAST(jam_out as TIME) < '16:00' THEN 'Shift 2'
                    WHEN CAST(jam_out as TIME) >= '16:00' AND CAST(jam_out as TIME) < '20:00' THEN 'Shift 3'
                    ELSE 'Outside'
                END as shift"
            )
            ->orderBy('jam_out', 'desc')
            ->paginate(10);
        // dd($dataout);
        return view('livewire.fgdashboard', [
            'datafgtruk' => $data,
            'data7hari' => $data7hari,
            'datatrukout' => $dataout,
            'antrianskr' => $antrianskr,
            'antrianbsk' => $antrianbsk,
            'registered' => $registrasi,
            'pendingkmr' => $pendingkmr,
            'tidakdatang' => $tidakdatang,
            'tmsdhmasuk' => $tmsdhmasuk,
            'shift1' => $shift1,
            'shift2' => $shift2,
            'shift3' => $shift3,
            'shiftOutside' => $shiftOutside,
            'quotaToday' => $quotaToday,
            'shiftChartData' => $shiftChartData
        ]);
    }
}
