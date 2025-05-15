<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKendaraanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'jenis_kendaraan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'tarif_perjam' => [
                'type' => 'INT',
                'null' => false,
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
        $this->forge->createTable('kendaraan');
    }

    public function down()
    {
        $this->forge->dropTable('kendaraan');
    }
}
