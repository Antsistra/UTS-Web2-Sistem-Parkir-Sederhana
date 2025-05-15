<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kendaraan_id', 'nomor_polisi', 'jam_masuk', 'jam_keluar', 'biaya', 'created_at', 'updated_at'];
}
