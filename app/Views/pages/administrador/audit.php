<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-3"><strong>Rejistu</strong> Auditoria</h1>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Aktividade Sistema</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Data</th>
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
    </div>
</div>

<?= $this->endSection(); ?>
