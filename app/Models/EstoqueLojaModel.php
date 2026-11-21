<?php

namespace App\Models;

use CodeIgniter\Model;

class EstoqueLojaModel extends Model
{
    protected $table         = 'estoque_lojas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['loja_id', 'produto_id', 'quantidade'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'         => 'permit_empty|is_natural_no_zero',
        'loja_id'    => 'required|is_natural_no_zero|is_not_unique[lojas.id]',
        'produto_id' => 'required|is_natural_no_zero|is_not_unique[produtos.id]',
        'quantidade' => 'required|is_natural|less_than[100000]',
    ];

    protected $validationMessages = [
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
        'quantidade' => [
            'required'   => 'Informe a quantidade.',
            'is_natural' => 'A quantidade deve ser um número inteiro, de zero para cima.',
            'less_than'  => 'A quantidade deve ser menor que 100.000.',
        ],
    ];

    /**
     * Lista o estoque por loja com os nomes da loja e do produto.
     */
    public function listar(?string $busca = null): array
    {
        $this->select('estoque_lojas.*, lojas.nome AS loja_nome, produtos.nome AS produto_nome')
            ->join('lojas', 'lojas.id = estoque_lojas.loja_id', 'left')
            ->join('produtos', 'produtos.id = estoque_lojas.produto_id', 'left');

        if ($busca !== null && $busca !== '') {
            $this->groupStart()
                ->like('lojas.nome', $busca)
                ->orLike('produtos.nome', $busca)
                ->groupEnd();
        }

        return $this->orderBy('lojas.nome', 'ASC')
            ->orderBy('produtos.nome', 'ASC')
            ->findAll();
    }
}
