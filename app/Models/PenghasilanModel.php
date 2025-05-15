<?php

namespace App\Models;

use CodeIgniter\Model;

class PenghasilanModel extends Model
{
    protected $table = 'penghasilan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['total_penghasilan', 'created_at', 'updated_at'];
}
