<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 

class Createspm extends Model
{
    use HasFactory;
    protected $connection = "sqlsrv";
    protected $table = "createspms";
    protected $guarded =[];
}
