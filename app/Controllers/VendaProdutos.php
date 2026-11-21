<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\LojaModel;
use App\Models\ProdutoModel;
use App\Models\VendaProdutoModel;
use CodeIgniter\HTTP\RedirectResponse;

class VendaProdutos extends BaseController
{
    private VendaProdutoModel $vendaProdutoModel;
    private ClienteModel $clienteModel;
    private LojaModel $lojaModel;
    private ProdutoModel $produtoModel;

    public function __construct()
    {
        $this->vendaProdutoModel = new VendaProdutoModel();
        $this->clienteModel      = new ClienteModel();
        $this->lojaModel         = new LojaModel();
        $this->produtoModel      = new ProdutoModel();
    }

    public function index(): string
    {
        $busca = trim((string) $this->request->getGet('busca'));

        return view('venda_produtos/index', [
            'titulo' => 'Vendas',
            'busca'  => $busca,
            'vendas' => $this->vendaProdutoModel->listar($busca),
        ]);
    }

    public function novo(): string
    {
        return view('venda_produtos/form', [
            'titulo'   => 'Nova venda',
            'acao'     => site_url('venda-produtos/salvar'),
            'venda'    => [
                'cliente_id'     => '',
                'loja_id'        => '',
                'produto_id'     => '',
                'data'           => date('Y-m-d H:i:s'),
                'quantidade'     => '1',
                'preco_unitario' => '',
            ],
            'clientes' => $this->clienteModel->orderBy('nome', 'ASC')->findAll(),
            'lojas'    => $this->lojaModel->orderBy('nome', 'ASC')->findAll(),
            'produtos' => $this->produtoModel->orderBy('nome', 'ASC')->findAll(),
        ]);
    }

    public function salvar(): RedirectResponse
    {
        if (! $this->vendaProdutoModel->insert($this->dadosDoFormulario())) {
            return redirect()->back()->withInput()->with('erros', $this->vendaProdutoModel->errors());
        }

        return redirect()->to('venda-produtos')->with('sucesso', 'Venda cadastrada com sucesso.');
    }

    public function editar(int $id): string|RedirectResponse
    {
        $venda = $this->vendaProdutoModel->find($id);

        if ($venda === null) {
            return redirect()->to('venda-produtos')->with('erros', ['Venda não encontrada.']);
        }

        return view('venda_produtos/form', [
            'titulo'   => 'Editar venda',
            'acao'     => site_url('venda-produtos/atualizar/' . $id),
            'venda'    => $venda,
            'clientes' => $this->clienteModel->orderBy('nome', 'ASC')->findAll(),
            'lojas'    => $this->lojaModel->orderBy('nome', 'ASC')->findAll(),
            'produtos' => $this->produtoModel->orderBy('nome', 'ASC')->findAll(),
        ]);
    }

    public function atualizar(int $id): RedirectResponse
    {
        if ($this->vendaProdutoModel->find($id) === null) {
            return redirect()->to('venda-produtos')->with('erros', ['Venda não encontrada.']);
        }

        // O "id" segue junto para as regras de validação do model.
        $dados = $this->dadosDoFormulario() + ['id' => $id];

        if (! $this->vendaProdutoModel->update($id, $dados)) {
            return redirect()->back()->withInput()->with('erros', $this->vendaProdutoModel->errors());
        }

        return redirect()->to('venda-produtos')->with('sucesso', 'Venda atualizada com sucesso.');
    }

    public function excluir(int $id): RedirectResponse
    {
        if ($this->vendaProdutoModel->find($id) === null) {
            return redirect()->to('venda-produtos')->with('erros', ['Venda não encontrada.']);
        }

        $this->vendaProdutoModel->delete($id);

        return redirect()->to('venda-produtos')->with('sucesso', 'Venda excluída com sucesso.');
    }

    /**
     * Converte o valor do input datetime-local (Y-m-d\TH:i) para Y-m-d H:i:s.
     */
    private function dataDoFormulario(): string
    {
        $data = str_replace('T', ' ', trim((string) $this->request->getPost('data')));

        if (strlen($data) === 16) {
            $data .= ':00';
        }

        return $data;
    }

    /**
     * Lê os campos do formulário de venda.
     */
    private function dadosDoFormulario(): array
    {
        return [
            'cliente_id'     => trim((string) $this->request->getPost('cliente_id')),
            'loja_id'        => trim((string) $this->request->getPost('loja_id')),
            'produto_id'     => trim((string) $this->request->getPost('produto_id')),
            'data'           => $this->dataDoFormulario(),
            'quantidade'     => trim((string) $this->request->getPost('quantidade')),
            'preco_unitario' => trim((string) $this->request->getPost('preco_unitario')),
        ];
    }
}
