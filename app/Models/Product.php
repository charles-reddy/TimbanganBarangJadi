<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     */
    protected $connection = 'sqlsrv';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'itemCode';

    /**
     * The "type" of the primary key ID.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    protected $fillable = ['itemCode', 'itemName', 'deduction', 'type'];
}
