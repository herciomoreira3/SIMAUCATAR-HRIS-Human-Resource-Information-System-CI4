<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-3"><strong>Rejistu</strong> Auditoria</h1>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Aktividade Sistema</h5>
    </div>
    <div class="card-body">
        <?php
        $query = service('request')->getGet();
        $pageUrl = static function (int $page) use ($query): string {
            $query['page'] = $page;
            return current_url() . '?' . http_build_query($query);
        };
        $sortUrl = static function () use ($query, $pagination): string {
            $query['sort'] = 'created_at';
            $query['direction'] = $pagination['direction'] === 'ASC' ? 'desc' : 'asc';
            $query['page'] = 1;
            return current_url() . '?' . http_build_query($query);
        };
        ?>
        <form method="get" class="d-flex justify-content-end mb-3">
            <input type="hidden" name="sort" value="created_at">
            <input type="hidden" name="direction" value="<?= esc(strtolower($pagination['direction'])) ?>">
            <label class="d-flex align-items-center gap-2 mb-0">
                <span class="text-muted">Dadus kada pajina</span>
                <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                    <?php foreach ([10, 25, 50, 100] as $perPage): ?>
                        <option value="<?= $perPage ?>" <?= $pagination['per_page'] === $perPage ? 'selected' : '' ?>><?= $perPage ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </form>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><a class="text-reset text-decoration-none" href="<?= esc($sortUrl()) ?>">Data<?= $pagination['direction'] === 'ASC' ? ' ▲' : ' ▼' ?></a></th>
                        <th>Ator</th>
                        <th>Papel</th>
                        <th>Asaun</th>
                        <th>Entidade</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= !empty($log['created_at']) ? date('d-m-Y H:i:s', strtotime($log['created_at'])) : '-' ?></td>
                            <td><?= esc($log['fullname'] ?? $log['username'] ?? 'Sistema') ?></td>
                            <td><?= esc($log['actor_role'] ?? '-') ?></td>
                            <td><span class="badge bg-primary"><?= esc($log['action']) ?></span></td>
                            <td><?= esc(($log['entity_type'] ?? '-') . (!empty($log['entity_id']) ? '#' . $log['entity_id'] : '')) ?></td>
                            <td><?= esc($log['ip_address'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="6" class="text-center text-muted">La iha audit log.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
            <small class="text-muted">Hatudu <?= $pagination['total'] === 0 ? 0 : $pagination['offset'] + 1 ?>–<?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> husi <?= $pagination['total'] ?> dadus</small>
            <?php if ($pagination['pages'] > 1): ?>
                <nav aria-label="Pajina rejistu auditoria">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?= $pagination['page'] <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= esc($pageUrl(max(1, $pagination['page'] - 1))) ?>">Atras</a></li>
                        <li class="page-item disabled"><span class="page-link"><?= $pagination['page'] ?> / <?= $pagination['pages'] ?></span></li>
                        <li class="page-item <?= $pagination['page'] >= $pagination['pages'] ? 'disabled' : '' ?>"><a class="page-link" href="<?= esc($pageUrl(min($pagination['pages'], $pagination['page'] + 1))) ?>">Oituan</a></li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
