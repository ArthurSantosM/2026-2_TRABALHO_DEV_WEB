<?= $this->extend('layouts/base') ?>

<?= $this->section('conteudo') ?>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <h1 class="h3 mb-3"><?= esc($titulo) ?></h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post" action="<?= esc($acao) ?>" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="cliente_id" class="form-label">Cliente *</label>
                        <?php $clienteEscolhido = (string) old('cliente_id', $venda['cliente_id'] ?? ''); ?>
                        <select id="cliente_id" name="cliente_id" class="form-select" required>
                            <option value="">Selecione um cliente</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= esc($cliente['id']) ?>"
                                    <?= $clienteEscolhido === (string) $cliente['id'] ? 'selected' : '' ?>>
                                    <?= esc($cliente['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6 mb-3">
                            <label for="loja_id" class="form-label">Loja *</label>
                            <?php $lojaEscolhida = (string) old('loja_id', $venda['loja_id'] ?? ''); ?>
                            <select id="loja_id" name="loja_id" class="form-select" required>
                                <option value="">Selecione uma loja</option>
                                <?php foreach ($lojas as $loja): ?>
                                    <option value="<?= esc($loja['id']) ?>"
                                        <?= $lojaEscolhida === (string) $loja['id'] ? 'selected' : '' ?>>
                                        <?= esc($loja['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 mb-3">
                            <label for="produto_id" class="form-label">Produto *</label>
                            <?php $produtoEscolhido = (string) old('produto_id', $venda['produto_id'] ?? ''); ?>
                            <select id="produto_id" name="produto_id" class="form-select" required>
                                <option value="">Selecione um produto</option>
                                <?php foreach ($produtos as $produto): ?>
                                    <option value="<?= esc($produto['id']) ?>"
                                        <?= $produtoEscolhido === (string) $produto['id'] ? 'selected' : '' ?>>
                                        <?= esc($produto['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-4 mb-3">
                            <label for="data" class="form-label">Data e hora *</label>
                            <?php $dataPadrao = str_replace(' ', 'T', substr((string) $venda['data'], 0, 16)); ?>
                            <input type="datetime-local" id="data" name="data" class="form-control"
                                   value="<?= esc(old('data', $dataPadrao)) ?>" required>
                        </div>
                        <div class="col-12 col-sm-4 mb-3">
                            <label for="quantidade" class="form-label">Quantidade *</label>
                            <input type="number" id="quantidade" name="quantidade" class="form-control" step="1" min="1"
                                   value="<?= esc(old('quantidade', $venda['quantidade'])) ?>" required>
                        </div>
                        <div class="col-12 col-sm-4 mb-3">
                            <label for="preco_unitario" class="form-label">Preço unit. (R$) *</label>
                            <input type="number" id="preco_unitario" name="preco_unitario" class="form-control" step="0.01" min="0.01"
                                   value="<?= esc(old('preco_unitario', $venda['preco_unitario'])) ?>" required>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Salvar</button>
                        <a href="<?= site_url('venda-produtos') ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
