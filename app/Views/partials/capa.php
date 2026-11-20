<?php if (!empty($l['capa'])): ?>
    <img src="<?= e(capa_url($l['capa'])) ?>" alt="Capa de <?= e($l['titulo']) ?>" class="capa" loading="lazy">
<?php else: ?>
    <div class="capa capa-gerada" style="--h: <?= capa_cor((int) $l['id']) ?>">
        <span class="capa-titulo"><?= e($l['titulo']) ?></span>
        <?php if (!empty($l['autor'])): ?><span class="capa-autor"><?= e($l['autor']) ?></span><?php endif; ?>
    </div>
<?php endif; ?>
