<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Customerlist extends Component
{
    use WithPagination;

    public $search = '';
    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $datacustomer = DB::connection('sqlsrv2')->table('users')
            ->when($this->search, function ($query) {
                return $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        return view('livewire.customerlist', ['datacustomer' => $datacustomer]);
    }

    public function resetPassword($userId)
    {
        $currentYear = date('Y');
        $defaultPassword = 'ktm' . $currentYear;

        DB::connection('sqlsrv2')->table('users')
            ->where('id', $userId)
            ->update([
                'password' => Hash::make($defaultPassword),
                'updated_at' => DB::raw('GETDATE()')
            ]);

        session()->flash('message', 'Password berhasil direset ke ' . $defaultPassword);
    }
}
