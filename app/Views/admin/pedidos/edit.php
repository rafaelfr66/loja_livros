<form action="<?= url("admin/pedidos/{$pedido['id']}/atualizar") ?>" method="post" class="painel form-admin" data-validar novalidate>
    <?= csrf_field() ?>
    <p>Cliente: <strong><?= e($pedido['cliente']) ?></strong> · Total: <strong><?= moeda($pedido['total']) ?></strong>
        · <?= count($pedido['itens']) ?> item(ns)</p>
    <label>Status
        <select name="status" required>
            <?php foreach (App\Models\Pedido::STATUS as $s): ?>
                <option value="<?= $s ?>" <?= old('status', $pedido['status']) === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <p class="texto-suave">Ao cancelar, os livros voltam ao estoque. Ao reativar um pedido cancelado, o estoque é baixado novamente.</p>
    <label>Observação
        <input type="text" name="observacao" value="<?= e(old('observacao', $pedido['observacao'] ?? '')) ?>" maxlength="255">
    </label>
    <div class="form-botoes">
        <a href="<?= url('admin/pedidos/' . $pedido['id']) ?>" class="btn btn-contorno">Cancelar</a>
        <button class="btn btn-primario">Salvar</button>
    </div>
</form>
