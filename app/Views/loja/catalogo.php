<div class="catalogo">
    <aside class="filtros">
        <h3>Gêneros</h3>
        <ul>
            <li><a href="<?= url('livros' . ($filtros['busca'] ? '?busca=' . urlencode($filtros['busca']) : '')) ?>"
                   class="<?= !$filtros['categoria'] ? 'ativo' : '' ?>">Todos</a></li>
            <?php foreach ($categorias as $c): ?>
                <li>
                    <a href="<?= url('livros?' . http_build_query(['categoria' => $c['id'], 'busca' => $filtros['busca']])) ?>"
                       class="<?= $filtros['categoria'] === (int) $c['id'] ? 'ativo' : '' ?>">
                        <?= e($c['nome']) ?> <span><?= (int) $c['qtd_livros'] ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <section>
        <form method="get" action="<?= url('livros') ?>" class="barra-filtros">
            <input type="hidden" name="categoria" value="<?= $filtros['categoria'] ?: '' ?>">
            <input type="search" name="busca" value="<?= e($filtros['busca']) ?>" placeholder="Título, autor ou ISBN">
            <select name="ordem" onchange="this.form.submit()">
                <?php foreach (['titulo' => 'Título (A-Z)', 'recentes' => 'Mais recentes', 'menor_preco' => 'Menor preço', 'maior_preco' => 'Maior preço'] as $v => $r): ?>
                    <option value="<?= $v ?>" <?= $filtros['ordem'] === $v ? 'selected' : '' ?>><?= $r ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primario">Buscar</button>
        </form>

        <p class="resultado-info">
            <?= count($livros) ?> livro(s) encontrado(s)
            <?php if ($filtros['busca']): ?> para “<strong><?= e($filtros['busca']) ?></strong>”<?php endif; ?>
        </p>

        <?php if (!$livros): ?>
            <div class="vazio">
                <p>📭 Nenhum livro encontrado com esses filtros.</p>
                <a href="<?= url('livros') ?>" class="btn btn-contorno">Limpar filtros</a>
            </div>
        <?php else: ?>
            <div class="grade-livros">
                <?php foreach ($livros as $l): ?>
                    <?php require APP_PATH . '/Views/partials/livro_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>
