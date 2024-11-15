<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JembatanTimbang extends Model
{
    use HasFactory;
    protected $connection = "sqlsrv";
    protected $table = "m_timbangan";
    protected $fillable =['timbanganID','timbanganName'];
}
