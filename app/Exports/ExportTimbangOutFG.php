<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportTimbangOutFG implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $tglin;
    protected $katakunciout;
    public $sortColumn = 'jam_in';
    public $sortDirection = 'asc';
    

    function __construct($tglin,$katakunciout) {
            $this->tglin = $tglin;
            $this->katakunciout = $katakunciout;
            
    }

    public function sort($columnName)
    {
        $this->sortColumn = $columnName;
        $this->sortDirection = $this->sortDirection == 'asc'?'desc' : 'asc';
        
    }

    public function collection()
    {
        $tglawal=date('d-m-Y',strtotime(Carbon::now()->subDay(4)));
        

        if (($this->katakunciout )  !=null) { 
            
            $hasil = DB::connection('sqlsrv')->table('trscale')
                ->join('customers', 'customers.custID', 'trscale.custID')
                ->join('products', 'products.itemCode', 'trscale.itemCode')
                ->leftJoin('createspms', 'createspms.id', 'trscale.spmID')
                ->leftJoin('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
                ->select('trscale.*', 'customers.custName', 'products.itemName', 'create_t_m_s.tmTranspName', 'createspms.sealNo', 'create_t_m_s.jamMuat',
                    DB::raw("CASE 
                        WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' 
                        WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' 
                        WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' 
                        ELSE 'Luar Jam Shift' 
                    END as shift_tiket")
                )
                ->where('driver','like','%' . $this->katakunciout . '%')
                ->whereNotNull('netto')
                ->orwhere('carID','like','%' . $this->katakunciout . '%')
                ->wheredate('jam_in','>=',$this->tglin)
                ->orderby($this->sortColumn ,$this->sortDirection)
                ->get();
            
        } elseif (($this->tglin  )  !=null) {
            
            $hasil = DB::connection('sqlsrv')->table('trscale')
                ->join('customers', 'customers.custID', 'trscale.custID')
                ->join('products', 'products.itemCode', 'trscale.itemCode')
                ->leftJoin('createspms', 'createspms.id', 'trscale.spmID')
                ->leftJoin('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
                ->select('trscale.*', 'customers.custName', 'products.itemName', 'create_t_m_s.tmTranspName', 'createspms.sealNo', 'create_t_m_s.jamMuat',
                    DB::raw("CASE 
                        WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' 
                        WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' 
                        WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' 
                        ELSE 'Luar Jam Shift' 
                    END as shift_tiket")
                )
                ->wheredate('jam_in','>=',$this->tglin)
                ->whereNotNull('netto')
                ->orderby($this->sortColumn ,$this->sortDirection)
                ->get();
        
        } else {
             
            $this->tglin = $tglawal;
            $hasil = DB::connection('sqlsrv')->table('trscale')
                ->join('customers', 'customers.custID', 'trscale.custID')
                ->join('products', 'products.itemCode', 'trscale.itemCode')
                ->leftJoin('createspms', 'createspms.id', 'trscale.spmID')
                ->leftJoin('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
                ->select('trscale.*', 'customers.custName', 'products.itemName', 'create_t_m_s.tmTranspName', 'createspms.sealNo', 'create_t_m_s.jamMuat',
                    DB::raw("CASE 
                        WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' 
                        WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' 
                        WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' 
                        ELSE 'Luar Jam Shift' 
                    END as shift_tiket")
                )
                ->wheredate('jam_in','>=',$this->tglin)
                ->whereNotNull('netto')
                ->orderby($this->sortColumn ,$this->sortDirection)
                ->get();
        
        }
        // dd($hasil);
        return $hasil;
    }

    public function headings(): array
        {
            //Put Here Header Name That you want in your excel sheet 
            return [
                'SO/SPPB',
                'PO',
                'Ekspedisi',
                'No Seal',
                'Driver',
                'Car ID',
                'Customer',
                'Item Name',
                'Bobot IN',
                'Bobot OUT',
                'Netto',
                'Date IN',
                'Date OUT',
                'Shift Tiket',
                'Shift Muat',
            ];
        }

        public function map($hasil): array
        {
            // Calculate Shift Muat based on jam_in
            $shiftMuat = '-';
            if (!is_null($hasil->jam_in)) {
                $jamIn = date('H:i', strtotime($hasil->jam_in));
                if ($jamIn >= '08:00' && $jamIn < '12:00') {
                    $shiftMuat = 'Shift 1 (08:00-12:00)';
                } elseif ($jamIn >= '12:00' && $jamIn < '16:00') {
                    $shiftMuat = 'Shift 2 (12:00-16:00)';
                } elseif ($jamIn >= '16:00' && $jamIn < '20:00') {
                    $shiftMuat = 'Shift 3 (16:00-20:00)';
                } else {
                    $shiftMuat = 'Luar Jam Shift';
                }
            }
            
            return [
                $hasil->doNo,
                $hasil->poNo,
                $hasil->tmTranspName ?? '-',
                $hasil->sealNo ?? '-',
                $hasil->driver,
                $hasil->carID,
                $hasil->custName,
                $hasil->itemName,
                $hasil->timbangin,
                $hasil->timbangout,
                $hasil->netto,
                date('d-m-Y H:i:s',strtotime( $hasil->jam_in)),
                date('d-m-Y H:i:s',strtotime( $hasil->jam_out)),
                $hasil->shift_tiket ?? '-',
                $shiftMuat,
            ];
        }
}
