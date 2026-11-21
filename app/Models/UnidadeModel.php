<?php

namespace App\Models;

use CodeIgniter\Model;

class UnidadeModel extends Model
{
    protected $table         = 'unidades';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['identificador', 'loja_id'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'            => 'permit_empty|is_natural_no_zero',
        'identificador' => 'required|min_length[2]|max_length[50]',
        'loja_id'       => 'permit_empty|is_natural_no_zero|is_not_unique[lojas.id]',
    ];

    protected $validationMessages = [
        'identificador' => [
            'required'   => 'Informe o identificador da unidade.',
            'min_length' => 'O identificador deve ter pelo menos 2 caracteres.',
            'max_length' => 'O identificador deve ter no máximo 50 caracteres.',
        ],
        'loja_id' => [
            'is_natural_no_zero' => 'A loja informada é inválida.',
            'is_not_unique'      => 'A loja informada não existe.',
        ],
    ];

    /**
     * Lista as unidades com o nome da loja, com busca opcional.
     */
    public function listar(?string $busca = null): array
    {
        $this->select('unidades.*, lojas.nome AS loja_nome')
            ->join('lojas', 'lojas.id = unidades.loja_id', 'left');

        if ($busca !== null && $busca !== '') {
            $this->groupStart()
                ->like('unidades.identificador', $busca)
                ->orLike('lojas.nome', $busca)
                ->groupEnd();
        }

        return $this->orderBy('lojas.nome', 'ASC')
            ->orderBy('unidades.identificador', 'ASC')
            ->findAll();
    }
}
