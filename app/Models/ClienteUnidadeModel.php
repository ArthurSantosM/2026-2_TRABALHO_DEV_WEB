<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteUnidadeModel extends Model
{
    protected $table         = 'cliente_unidades';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['cliente_id', 'unidade_id'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'         => 'permit_empty|is_natural_no_zero',
        'cliente_id' => 'required|is_natural_no_zero|is_not_unique[clientes.id]',
        'unidade_id' => 'required|is_natural_no_zero|is_not_unique[unidades.id]',
    ];

    protected $validationMessages = [
        'cliente_id' => [
            'required'           => 'Informe o cliente.',
            'is_natural_no_zero' => 'O cliente informado é inválido.',
            'is_not_unique'      => 'O cliente informado não existe.',
        ],
        'unidade_id' => [
            'required'           => 'Informe a unidade.',
            'is_natural_no_zero' => 'A unidade informada é inválida.',
            'is_not_unique'      => 'A unidade informada não existe.',
        ],
    ];

    /**
     * Lojas onde o cliente possui pelo menos uma unidade vinculada.
     */
    public function lojasDoCliente(int $clienteId): array
    {
        return $this->db->table('cliente_unidades')
            ->distinct()
            ->select('lojas.id, lojas.nome')
            ->join('unidades', 'unidades.id = cliente_unidades.unidade_id')
            ->join('lojas', 'lojas.id = unidades.loja_id')
            ->where('cliente_unidades.cliente_id', $clienteId)
            ->orderBy('lojas.nome', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Unidades do cliente que pertencem a uma loja específica.
     */
    public function unidadesDoClienteNaLoja(int $clienteId, int $lojaId): array
    {
        return $this->db->table('cliente_unidades')
            ->select('unidades.id, unidades.identificador AS nome')
            ->join('unidades', 'unidades.id = cliente_unidades.unidade_id')
            ->where('cliente_unidades.cliente_id', $clienteId)
            ->where('unidades.loja_id', $lojaId)
            ->orderBy('unidades.identificador', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Lista os vínculos com nome do cliente, unidade e loja.
     */
    public function listar(?string $busca = null): array
    {
        $this->select('cliente_unidades.*, clientes.nome AS cliente_nome, unidades.identificador AS unidade_nome, lojas.nome AS loja_nome')
            ->join('clientes', 'clientes.id = cliente_unidades.cliente_id', 'left')
            ->join('unidades', 'unidades.id = cliente_unidades.unidade_id', 'left')
            ->join('lojas', 'lojas.id = unidades.loja_id', 'left');

        if ($busca !== null && $busca !== '') {
            $this->groupStart()
                ->like('clientes.nome', $busca)
                ->orLike('unidades.identificador', $busca)
                ->orLike('lojas.nome', $busca)
                ->groupEnd();
        }

        return $this->orderBy('clientes.nome', 'ASC')
            ->orderBy('unidades.identificador', 'ASC')
            ->findAll();
    }
}
