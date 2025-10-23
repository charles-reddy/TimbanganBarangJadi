<?php

namespace App\Console;

use App\Imports\ImportSupplier;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {

            $cek = DB::connection('sqlsrv')->table('create_t_m_s')->leftJoin('createsppbs','createsppbs.id','create_t_m_s.tmSppbID' )->leftJoin('products', 'products.itemCode', 'create_t_m_s.itemCode')->select('create_t_m_s.id','create_t_m_s.tmSppbID','pendfno','tmQtyKg','itemName','tglMuat', 'sppbQtyKg','openQtyKg','isSecCek','createsppbs.id as sppbID')->whereDate('tglMuat','<', date('Y-m-d',strtotime(Carbon::now()->addDays(-1))) )->whereNull('create_t_m_s.isSecCek')->where('create_t_m_s.tmqtykg','>', 0)->orderBy('create_t_m_s.id','desc')->get();
        // dd($cek);

        foreach ($cek as $c) {
            $balance = $c->tmQtyKg + $c->openQtyKg;
            // dd($balance);
            
            DB::connection('sqlsrv')->table('createsppbs')->where('createsppbs.id',$c->tmSppbID)->update([
                'openQtyKg' => $balance,
            ]);

            DB::connection('sqlsrv')->table('create_t_m_s')->where('id',$c->id)->update([
                'tmQtyKg' => 0,
            ]);
        }

        // dd('selesai');

        })->dailyAt('23:53');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
