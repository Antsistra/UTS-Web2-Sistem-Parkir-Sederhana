<?php

namespace App\Models;

use CodeIgniter\Model;

class KendaraanModel extends Model
{
    protected $table = 'kendaraan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['jenis_kendaraan', 'tarif_perjam', 'created_at', 'updated_at'];
}
