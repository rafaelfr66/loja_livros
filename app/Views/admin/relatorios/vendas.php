<form method="get" class="painel filtro-inline">
    <label>De <input type="date" name="inicio" value="<?= e($inicio) ?>" required></label>
    <label>Até <input type="date" name="fim" value="<?= e($fim) ?>" required></label>
    <button class="btn btn-primario">Gerar relatório</button>
    <button type="button" class="btn btn-contorno" onclick="window.print()">🖨 Imprimir</button>
</form>

<div class="cards-indicadores">
    <div class="indicador"><span>Pedidos no período</span><strong><?= (int) $resumo['pedidos'] ?></strong></div>
    <div class="indicador"><span>Faturamento</span><strong><?= moeda($resumo['faturamento']) ?></strong></div>
    <div class="indicador"><span>Ticket médio</span><strong><?= moeda($resumo['ticket_medio']) ?></strong></div>
</div>

<div class="grade-2">
    <section class="painel">
        <h2>Livros mais vendidos</h2>
        <div class="tabela-wrap">
            <table class="tabela">
                <thead><tr><th>Livro</th><th>Qtd.</th><th>Total</th></tr></thead>
                <tbody>
                <?php foreach ($livros as $l): ?>
                    <tr>
                        <td><strong><?= e($l['titulo']) ?></strong><br><small class="texto-suave"><?= e($l['autor']) ?></small></td>
                        <td><?= (int) $l['quantidade'] ?></td>
                        <td><?= moeda($l['total']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$livros): ?><tr><td colspan="3" class="texto-suave">Sem vendas no período.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="painel">
        <h2>Vendas por categoria</h2>
        <?php $maxCat = $categorias ? max(array_column($categorias, 'total')) : 1; ?>
        <?php foreach ($categorias as $c): ?>
            <div class="barra-horizontal">
                <div class="barra-horizontal-rotulo"><span><?= e($c['categoria']) ?></span><span><?= moeda($c['total']) ?> · <?= (int) $c['quantidade'] ?> un.</span></div>
                <div class="barra-horizontal-trilho"><div style="width: <?= round($c['total'] / $maxCat * 100) ?>%"></div></div>
            </div>
        <?php endforeach; ?>
        <?php if (!$categorias): ?><p class="texto-suave">Sem vendas no período.</p><?php endif; ?>
    </section>
</div>
