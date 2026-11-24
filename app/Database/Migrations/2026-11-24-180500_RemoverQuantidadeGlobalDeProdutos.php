<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * O estoque passa a ter uma única fonte de verdade: estoque_lojas.
 * O "estoque global" exibido na tela de produtos é a soma por loja,
 * calculada no ProdutoModel, então a coluna antiga deixa de existir.
 */
class RemoverQuantidadeGlobalDeProdutos extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('quantidade', 'produtos')) {
            $this->forge->dropColumn('produtos', 'quantidade');
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('quantidade', 'produtos')) {
            return;
        }

        $this->forge->addColumn('produtos', [
            'quantidade' => [
                'type'    => 'INT',
                'default' => 0,
            ],
        ]);

        // Reconstrói o valor antigo a partir do estoque por loja.
        $this->db->query(
            'UPDATE `produtos` SET `quantidade` = COALESCE('
            . '(SELECT SUM(el.`quantidade`) FROM `estoque_lojas` el '
            . 'WHERE el.`produto_id` = `produtos`.`id`), 0)'
        );
    }
}
