<?php

namespace App\Database\Seeds;

use App\Models\ClienteModel;
use App\Models\LojaModel;
use App\Models\ProdutoModel;
use App\Models\VendaProdutoModel;
use CodeIgniter\Database\Seeder;

class VendaProdutoSeeder extends Seeder
{
    public function run()
    {
        $model = new VendaProdutoModel();

        // Só popula se a tabela estiver vazia, assim o seeder pode rodar várias vezes.
        if ($model->countAllResults() > 0) {
            return;
        }

        $clienteModel = new ClienteModel();
        $lojaModel    = new LojaModel();
        $produtoModel = new ProdutoModel();

        $vendas = [
            ['Camila Souza', 'Loja Centro', 'Barrinha de Cereal', '2026-11-20 09:35:00', 2, '4.50'],
            ['Rafael Lima', 'Loja Shopping', 'Whey Protein 20g', '2026-11-21 12:10:00', 1, '8.90'],
            ['Camila Souza', 'Loja Terminal', 'Água Mineral 500ml', '2026-11-21 18:42:00', 3, '3.00'],
        ];

        foreach ($vendas as [$nomeCliente, $nomeLoja, $nomeProduto, $data, $quantidade, $precoUnitario]) {
            $cliente = $clienteModel->where('nome', $nomeCliente)->first();
            $loja    = $lojaModel->where('nome', $nomeLoja)->first();
            $produto = $produtoModel->where('nome', $nomeProduto)->first();

            $model->insert([
                'cliente_id'     => $cliente['id'] ?? null,
                'loja_id'        => $loja['id'] ?? null,
                'produto_id'     => $produto['id'] ?? null,
                'data'           => $data,
                'quantidade'     => $quantidade,
                'preco_unitario' => $precoUnitario,
            ]);
        }
    }
}
