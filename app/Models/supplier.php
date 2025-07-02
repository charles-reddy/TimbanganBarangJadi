<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class supplier extends Model
{
    use HasFactory;
    protected $connection = "sqlsrv";
    protected $table = "suppliers";
    protected $fillable =['suppID','suppName'];
}
