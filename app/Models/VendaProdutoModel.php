<?php

namespace App\Models;

use CodeIgniter\Model;

class VendaProdutoModel extends Model
{
    protected $table         = 'venda_produtos';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['cliente_id', 'loja_id', 'produto_id', 'unidade_id', 'data', 'quantidade', 'preco_unitario'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'             => 'permit_empty|is_natural_no_zero',
        'cliente_id'     => 'required|is_natural_no_zero|is_not_unique[clientes.id]',
        'loja_id'        => 'required|is_natural_no_zero|is_not_unique[lojas.id]',
        'produto_id'     => 'required|is_natural_no_zero|is_not_unique[produtos.id]',
        'unidade_id'     => 'required|is_natural_no_zero|is_not_unique[unidades.id]',
        'data'           => 'required|valid_date[Y-m-d H:i:s]',
        'quantidade'     => 'required|is_natural_no_zero|less_than[100000]',
        'preco_unitario' => 'required|decimal|greater_than[0]|less_than[100000]',
    ];

    protected $validationMessages = [
        'cliente_id' => [
            'required'           => 'Informe o cliente.',
            'is_natural_no_zero' => 'O cliente informado é inválido.',
            'is_not_unique'      => 'O cliente informado não existe.',
        ],
        'loja_id' => [
            'required'           => 'Informe a loja.',
            'is_natural_no_zero' => 'A loja informada é inválida.',
            'is_not_unique'      => 'A loja informada não existe.',
        ],
        'produto_id' => [
            'required'           => 'Informe o produto.',
            'is_natural_no_zero' => 'O produto informado é inválido.',
            'is_not_unique'      => 'O produto informado não existe.',
        ],
        'unidade_id' => [
            'required'           => 'Informe a unidade.',
            'is_natural_no_zero' => 'A unidade informada é inválida.',
            'is_not_unique'      => 'A unidade informada não existe.',
        ],
        'data' => [
            'required'    => 'Informe a data da venda.',
            'valid_date'  => 'Informe uma data válida.',
        ],
        'quantidade' => [
            'required'           => 'Informe a quantidade vendida.',
            'is_natural_no_zero' => 'A quantidade deve ser um número inteiro maior que zero.',
            'less_than'          => 'A quantidade deve ser menor que 100.000.',
        ],
        'preco_unitario' => [
            'required'     => 'Informe o preço unitário.',
            'decimal'      => 'Informe um preço válido, por exemplo 4.50.',
            'greater_than' => 'O preço unitário deve ser maior que zero.',
            'less_than'    => 'O preço unitário deve ser menor que 100.000.',
        ],
    ];

    /**
     * Lista as vendas com os nomes de cliente, loja e produto, com busca opcional.
     */
    public function listar(?string $busca = null): array
    {
        $this->select('venda_produtos.*, clientes.nome AS cliente_nome, lojas.nome AS loja_nome, produtos.nome AS produto_nome, unidades.identificador AS unidade_nome')
            ->join('clientes', 'clientes.id = venda_produtos.cliente_id', 'left')
            ->join('lojas', 'lojas.id = venda_produtos.loja_id', 'left')
            ->join('produtos', 'produtos.id = venda_produtos.produto_id', 'left')
            ->join('unidades', 'unidades.id = venda_produtos.unidade_id', 'left');

        if ($busca !== null && $busca !== '') {
            $this->groupStart()
                ->like('clientes.nome', $busca)
                ->orLike('lojas.nome', $busca)
                ->orLike('produtos.nome', $busca)
                ->groupEnd();
        }

        return $this->orderBy('venda_produtos.data', 'DESC')
            ->orderBy('venda_produtos.id', 'DESC')
            ->findAll();
    }
}
