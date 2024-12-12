<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Createsppb extends Model
{
    use HasFactory;
    protected $connection = "sqlsrv";
    protected $table = "createsppbs";
    protected $guarded =[];
}
