<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MigrarUnidadesClientes extends Migration
{
    /**
     * Transforma o texto de clientes.unidade em registros de unidades e vínculos,
     * preservando os dados já cadastrados antes de remover a coluna antiga.
     */
    public function up()
    {
        $loja = $this->db->table('lojas')->orderBy('id', 'ASC')->get()->getRowArray();
        $lojaId = $loja['id'] ?? null;

        $clientes = $this->db->table('clientes')
            ->select('id, unidade')
            ->where('unidade IS NOT NULL')
            ->where('unidade !=', '')
            ->get()
            ->getResultArray();

        $agora = date('Y-m-d H:i:s');

        foreach ($clientes as $cliente) {
            $identificador = trim($cliente['unidade']);
            if ($identificador === '') {
                continue;
            }

            $unidade = $this->db->table('unidades')
                ->where('identificador', $identificador)
                ->where('loja_id', $lojaId)
                ->get()
                ->getRowArray();

            if ($unidade === null) {
                $this->db->table('unidades')->insert([
                    'identificador' => $identificador,
                    'loja_id'       => $lojaId,
                    'created_at'    => $agora,
                    'updated_at'    => $agora,
                ]);
                $unidadeId = $this->db->insertID();
            } else {
                $unidadeId = $unidade['id'];
            }

            $vinculo = $this->db->table('cliente_unidades')
                ->where('cliente_id', $cliente['id'])
                ->where('unidade_id', $unidadeId)
                ->get()
                ->getRowArray();

            if ($vinculo === null) {
                $this->db->table('cliente_unidades')->insert([
                    'cliente_id' => $cliente['id'],
                    'unidade_id' => $unidadeId,
                    'created_at' => $agora,
                    'updated_at' => $agora,
                ]);
            }
        }

        $this->forge->dropColumn('clientes', 'unidade');
    }

    public function down()
    {
        $this->forge->addColumn('clientes', [
            'unidade' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
        ]);

        $this->db->query(
            'UPDATE `clientes` c SET `unidade` = ('
            . 'SELECT u.`identificador` FROM `cliente_unidades` cu '
            . 'JOIN `unidades` u ON u.`id` = cu.`unidade_id` '
            . 'WHERE cu.`cliente_id` = c.`id` LIMIT 1)'
        );
    }
}
