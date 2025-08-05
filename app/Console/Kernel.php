<?php

namespace App\Console;

use App\Imports\ImportSupplier;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
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

             $directory = 'temp'; // e.g., 'public/uploads' or 'private/documents'
            $files = Storage::files($directory);
            Excel::import(new ImportSupplier, storage_path('app/' . $files[0]));
            Storage::delete($files[0]);

        })->dailyAt('21:23');
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
