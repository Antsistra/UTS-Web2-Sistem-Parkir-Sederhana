<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransaksiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'kendaraan_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => false,
            ],
            'nomor_polisi' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'jam_masuk' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'jam_keluar' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'biaya' => [
                'type' => 'INT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
                'default' => '0000-00-00 00:00:00',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
                'default' => '0000-00-00 00:00:00',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kendaraan_id', 'kendaraan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('transaksi');
    }

    public function down()
    {
        $this->forge->dropTable('transaksi');
    }
}
