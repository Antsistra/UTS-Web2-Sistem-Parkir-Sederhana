<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenghasilanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'total_penghasilan' => [
                'type' => 'INT',
                'null' => false,
                'default' => 0,
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
        $this->forge->createTable('penghasilan');
    }

    public function down()
    {
        $this->forge->dropTable('penghasilan');
    }
}
