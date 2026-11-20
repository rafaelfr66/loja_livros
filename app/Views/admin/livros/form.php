<?php
$acao = $livro ? "admin/livros/{$livro['id']}/atualizar" : 'admin/livros/salvar';
$v = fn(string $campo, $padrao = '') => old($campo, $livro[$campo] ?? $padrao);

$temOld = isset($_SESSION['old']);
$marcado = fn(string $campo, bool $padrao) => $temOld ? !empty($_SESSION['old'][$campo]) : (bool) ($livro[$campo] ?? $padrao);
?>
<form action="<?= url($acao) ?>" method="post" enctype="multipart/form-data" class="painel form-admin form-livro" data-validar novalidate>
    <?= csrf_field() ?>
    <div class="form-livro-campos">
        <label>Título *
            <input type="text" name="titulo" value="<?= e($v('titulo')) ?>" required maxlength="150">
        </label>
        <div class="linha-campos">
            <label>ISBN *
                <input type="text" name="isbn" value="<?= e($v('isbn')) ?>" required pattern="[0-9Xx\-]{10,17}" placeholder="978-85-..." >
            </label>
            <label>Editora
                <input type="text" name="editora" value="<?= e($v('editora')) ?>" maxlength="100">
            </label>
        </div>
        <div class="linha-campos">
            <label>Categoria *
                <select name="categoria_id" required>
                    <option value="">Selecione…</option>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= (int) $v('categoria_id') === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Autor *
                <select name="autor_id" required>
                    <option value="">Selecione…</option>
                    <?php foreach ($autores as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= (int) $v('autor_id') === (int) $a['id'] ? 'selected' : '' ?>><?= e($a['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
        <div class="linha-campos linha-4">
            <label>Preço (R$) *
                <input type="number" name="preco" value="<?= e($v('preco')) ?>" required min="0.01" step="0.01">
            </label>
            <label>Estoque *
                <input type="number" name="estoque" value="<?= e($v('estoque', 0)) ?>" required min="0" step="1">
            </label>
            <label>Ano
                <input type="number" name="ano_publicacao" value="<?= e($v('ano_publicacao')) ?>" min="1000" max="<?= date('Y') ?>">
            </label>
            <label>Páginas
                <input type="number" name="paginas" value="<?= e($v('paginas')) ?>" min="1">
            </label>
        </div>
        <label>Sinopse
            <textarea name="sinopse" rows="5"><?= e($v('sinopse')) ?></textarea>
        </label>
        <div class="checks">
            <label class="check"><input type="checkbox" name="ativo" value="1" <?= $marcado('ativo', true) ? 'checked' : '' ?>> Ativo (visível na loja)</label>
            <label class="check"><input type="checkbox" name="destaque" value="1" <?= $marcado('destaque', false) ? 'checked' : '' ?>> Destaque na página inicial</label>
        </div>
    </div>

    <div class="form-livro-capa">
        <span class="rotulo">Capa</span>
        <div class="preview-capa" id="preview-capa">
            <?php if ($livro): $l = $livro; require APP_PATH . '/Views/partials/capa.php'; else: ?>
                <div class="capa capa-vazia">Sem imagem</div>
            <?php endif; ?>
        </div>
        <input type="file" name="capa" accept="image/jpeg,image/png,image/webp,image/gif" data-preview="#preview-capa">
        <small class="texto-suave">JPG, PNG, WEBP ou GIF até 2 MB.</small>
        <?php if (!empty($livro['capa'])): ?>
            <label class="check"><input type="checkbox" name="remover_capa" value="1"> Remover capa atual</label>
        <?php endif; ?>
    </div>

    <div class="form-botoes">
        <a href="<?= url('admin/livros') ?>" class="btn btn-contorno">Cancelar</a>
        <button class="btn btn-primario">Salvar</button>
    </div>
</form>
