<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Monitortiketmuat extends Component
{
    public $pendfno;
    public $ticketData;
    public $notFound = false;

    public function search()
    {
        $this->notFound = false;
        $this->ticketData = null;

        if (empty($this->pendfno)) {
            session()->flash('error', 'Silakan masukkan nomor tiket muat');
            return;
        }

        // Get main ticket data
        $ticket = DB::connection('sqlsrv')
            ->table('create_t_m_s')
            ->leftJoin('customers', 'customers.custID', '=', 'create_t_m_s.custID')
            ->leftJoin('products', 'products.itemCode', '=', 'create_t_m_s.itemCode')
            ->leftJoin('createsppbs', 'createsppbs.id', '=', 'create_t_m_s.tmSppbID')
            ->leftJoin('jenistruks', 'jenistruks.id', '=', 'create_t_m_s.jenisTruk')
            ->select(
                'create_t_m_s.*',
                'customers.custName',
                'products.itemName',
                'createsppbs.sppbNo',
                'jenistruks.jenisTruk'
            )
            ->where('create_t_m_s.pendfNo', $this->pendfno)
            ->first();

        if (!$ticket) {
            $this->notFound = true;
            session()->flash('error', 'Tiket muat tidak ditemukan');
            return;
        }

        // Get SPM data if exists
        $spm = DB::connection('sqlsrv')
            ->table('createspms')
            ->where('tiketID', $ticket->id)
            ->first();

        // Get weighing/scale data if exists
        $scale = null;
        if ($spm) {
            $scale = DB::connection('sqlsrv')
                ->table('trscale')
                ->where('spmID', $spm->id)
                ->first();
        }

        $this->ticketData = [
            'ticket' => $ticket,
            'spm' => $spm,
            'scale' => $scale
        ];

        session()->flash('message', 'Data tiket berhasil ditemukan');
    }

    public function clear()
    {
        $this->pendfno = '';
        $this->ticketData = null;
        $this->notFound = false;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.monitortiketmuat');
    }
}
