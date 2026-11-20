<div class="cards-indicadores">
    <div class="indicador">
        <span>Faturamento</span>
        <strong><?= moeda($resumo['faturamento']) ?></strong>
        <small>pedidos não cancelados</small>
    </div>
    <div class="indicador">
        <span>Pedidos</span>
        <strong><?= (int) $resumo['total_pedidos'] ?></strong>
        <small><?= (int) $resumo['pendentes'] ?> pendente(s)</small>
    </div>
    <div class="indicador">
        <span>Ticket médio</span>
        <strong><?= moeda($resumo['ticket_medio']) ?></strong>
        <small>por pedido</small>
    </div>
    <div class="indicador">
        <span>Catálogo / Clientes</span>
        <strong><?= $totalLivros ?> / <?= $totalClientes ?></strong>
        <small>livros / clientes</small>
    </div>
</div>

<div class="grade-2">
    <section class="painel">
        <h2>Vendas nos últimos 6 meses</h2>
        <?php $max = max(array_column($vendasMes, 'total')) ?: 1; ?>
        <div class="grafico-barras">
            <?php foreach ($vendasMes as $m): ?>
                <div class="barra-col" title="<?= moeda($m['total']) ?> em <?= $m['qtd'] ?> pedido(s)">
                    <span class="barra-valor"><?= $m['total'] > 0 ? moeda($m['total']) : '' ?></span>
                    <div class="barra" style="height: <?= max(2, round($m['total'] / $max * 100)) ?>%"></div>
                    <span class="barra-rotulo"><?= date('m/Y', strtotime($m['mes'] . '-01')) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="painel">
        <h2>⚠ Estoque baixo (≤ 5)</h2>
        <?php if (!$estoqueBaixo): ?>
            <p class="texto-suave">Nenhum livro com estoque baixo.</p>
        <?php else: ?>
            <ul class="lista-simples">
                <?php foreach ($estoqueBaixo as $l): ?>
                    <li>
                        <a href="<?= url("admin/livros/{$l['id']}/editar") ?>"><?= e($l['titulo']) ?></a>
                        <span class="badge <?= $l['estoque'] == 0 ? 'badge-cancelado' : 'badge-pendente' ?>"><?= (int) $l['estoque'] ?> un.</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</div>

<section class="painel">
    <div class="painel-cabecalho">
        <h2>Últimos pedidos</h2>
        <a href="<?= url('admin/pedidos') ?>">Ver todos →</a>
    </div>
    <div class="tabela-wrap">
        <table class="tabela">
            <thead><tr><th>#</th><th>Cliente</th><th>Data</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($ultimosPedidos as $p): ?>
                <tr>
                    <td><a href="<?= url('admin/pedidos/' . $p['id']) ?>">#<?= $p['id'] ?></a></td>
                    <td><?= e($p['cliente']) ?></td>
                    <td><?= data_br($p['data_pedido'], true) ?></td>
                    <td><?= moeda($p['total']) ?></td>
                    <td><?= status_pedido($p['status']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
