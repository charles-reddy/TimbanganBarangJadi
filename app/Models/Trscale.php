<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trscale extends Model
{
    use HasFactory;
    protected $connection = "sqlsrv";
    protected $table = "trscale";
    protected $guarded =[];
}
