<?php
$query = service('request')->getGet();
$pageUrl = static function (int $page) use ($query): string {
    $query['page'] = $page;
    return current_url() . '?' . http_build_query($query);
};
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
    <small class="text-muted">Hatudu <?= $pagination['total'] === 0 ? 0 : $pagination['offset'] + 1 ?>–<?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> husi <?= $pagination['total'] ?> dadus</small>
    <?php if ($pagination['pages'] > 1): ?>
        <nav aria-label="Pajina relatóriu">
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?= $pagination['page'] <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= esc($pageUrl(max(1, $pagination['page'] - 1))) ?>">Atras</a></li>
                <li class="page-item disabled"><span class="page-link"><?= $pagination['page'] ?> / <?= $pagination['pages'] ?></span></li>
                <li class="page-item <?= $pagination['page'] >= $pagination['pages'] ? 'disabled' : '' ?>"><a class="page-link" href="<?= esc($pageUrl(min($pagination['pages'], $pagination['page'] + 1))) ?>">Oituan</a></li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
