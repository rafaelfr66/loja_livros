<form action="<?= url('admin/pedidos/salvar') ?>" method="post" class="painel form-admin" data-validar novalidate>
    <?= csrf_field() ?>
    <div class="linha-campos">
        <label>Cliente *
            <select name="cliente_id" required>
                <option value="">Selecione…</option>
                <?php foreach ($clientes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= (int) old('cliente_id') === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?> — <?= e($c['email']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Status inicial
            <select name="status">
                <option value="pendente">Pendente</option>
                <option value="pago" <?= old('status') === 'pago' ? 'selected' : '' ?>>Pago</option>
            </select>
        </label>
    </div>

    <span class="rotulo">Itens do pedido *</span>
    <table class="tabela tabela-itens" id="itens-pedido">
        <thead><tr><th>Livro</th><th style="width:110px">Qtd.</th><th style="width:130px">Subtotal</th><th style="width:50px"></th></tr></thead>
        <tbody>
            <tr class="item-linha">
                <td>
                    <select name="livro_id[]" required>
                        <option value="">Selecione…</option>
                        <?php foreach ($livros as $l): ?>
                            <option value="<?= $l['id'] ?>" data-preco="<?= $l['preco'] ?>" <?= $l['estoque'] == 0 ? 'disabled' : '' ?>>
                                <?= e($l['titulo']) ?> — <?= moeda($l['preco']) ?> (estoque: <?= (int) $l['estoque'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><input type="number" name="quantidade[]" value="1" min="1" required></td>
                <td class="item-subtotal">R$ 0,00</td>
                <td><button type="button" class="btn-icone" data-remover-item title="Remover">✕</button></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td><button type="button" class="btn btn-sm btn-contorno" id="adicionar-item">+ Adicionar livro</button></td>
                <th>Total</th>
                <th id="total-pedido">R$ 0,00</th>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <label>Observação
        <input type="text" name="observacao" value="<?= e(old('observacao')) ?>" maxlength="255">
    </label>
    <p class="texto-suave">O preço de cada item é o preço atual do livro, e o estoque é baixado automaticamente.</p>
    <div class="form-botoes">
        <a href="<?= url('admin/pedidos') ?>" class="btn btn-contorno">Cancelar</a>
        <button class="btn btn-primario">Criar pedido</button>
    </div>
</form>
