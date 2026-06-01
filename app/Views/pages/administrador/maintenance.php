<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-3"><strong>Kopia Seguransa</strong> no Manutensaun</h1>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Kopia Seguransa Database</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('administrador/maintenance/backup') ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary w-100">Kria Kopia Agora</button>
                </form>
                <hr>
                <form action="<?= base_url('administrador/maintenance/restore') ?>" method="post" enctype="multipart/form-data" onsubmit="return confirm('Restaura sei hakerek fila fali database atual. Kontinua?')">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Restaura SQL</label>
                        <input type="file" name="backup_file" class="form-control" accept=".sql" required>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">Restaura Kopia</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Lista Kopia Seguransa</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Fail</th>
                                <th>Tamañu</th>
                                <th>Data</th>
                                <th>Asaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($backups as $backup): ?>
                                <tr>
                                    <td><?= esc($backup['name']) ?></td>
                                    <td><?= number_format($backup['size'] / 1024, 1) ?> KB</td>
                                    <td><?= date('d-m-Y H:i', strtotime($backup['modified_at'])) ?></td>
                                    <td>
                                        <a class="btn btn-success btn-sm" href="<?= base_url('administrador/maintenance/backup/download/' . $backup['name']) ?>">Hatun</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($backups)): ?>
                                <tr><td colspan="4" class="text-center text-muted">Seidauk iha kopia seguransa.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
