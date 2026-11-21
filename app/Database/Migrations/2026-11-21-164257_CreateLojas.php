<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLojas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'endereco' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'franquia_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        // TODO: quando a migration de franquias existir, adicionar:
        // $this->forge->addForeignKey('franquia_id', 'franquias', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('lojas');
    }

    public function down()
    {
        $this->forge->dropTable('lojas');
    }
}
