<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Createspm extends Model
{
    use HasFactory;
    protected $connection = "sqlsrv";
    protected $table = "createspms";
    protected $guarded = [];

    /**
     * Relationship to TrscaleDetail (for multi-product weighing)
     */
    public function trscaleDetails()
    {
        return $this->hasMany(TrscaleDetail::class, 'spm_id');
    }

    /**
     * Relationship to Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'custID', 'custID');
    }

    /**
     * Relationship to Transporter
     */
    public function transporter()
    {
        return $this->belongsTo(Transporter::class, 'transpID', 'transpID');
    }

    /**
     * Relationship to Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'itemCode', 'itemCode');
    }

    /**
     * Relationship to Createsppb (SPPB)
     */
    public function sppb()
    {
        return $this->belongsTo(Createsppb::class, 'sppbNo', 'id');
    }

    /**
     * Relationship to createTM (Tiket)
     */
    public function tiket()
    {
        return $this->belongsTo(createTM::class, 'tiketID', 'id');
    }
}
