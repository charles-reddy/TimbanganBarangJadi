<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ImportSupplier implements ToCollection, WithStartRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) 
        {
            // dd($row[0]);
            DB::connection('sqlsrv')->table('suppliers')->insert([
                'suppName' => $row[0],
                'suppAdd' => $row[1],
            ]);
        }
        
        
    }

    public function startRow(): int
    {
        return 2;
    }
}
