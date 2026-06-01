<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-3"><strong>Balansu</strong> Lisensa</h1>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Jera Balansu Tinan</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('administrador/lisensa/balansu/generate') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Tinan</label>
                        <input type="number" name="year" class="form-control" value="<?= esc($year) ?>" min="2000" max="2100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipu Lisensa</label>
                        <input type="text" name="leave_type" class="form-control" value="Anuál" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hak Loron</label>
                        <input type="number" name="entitlement_days" class="form-control" value="12" min="1" step="0.5" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Jera</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Balansu Tinan <?= esc($year) ?></h5>
                <form method="get" class="d-flex gap-2">
                    <input type="number" name="year" class="form-control form-control-sm" value="<?= esc($year) ?>" min="2000" max="2100">
                    <button type="submit" class="btn btn-secondary btn-sm">Filtra</button>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th>Funsionariu</th>
                                <th>Tipu</th>
                                <th>Hak</th>
                                <th>Usadu</th>
                                <th>Pendente</th>
                                <th>Restu</th>
                                <th>Asaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($balances as $b): ?>
                                <tr>
                                    <td><?= esc($b['nid'] ?? '-') ?> - <?= esc($b['naran_kompletu'] ?? '-') ?></td>
                                    <td><?= esc($b['leave_type']) ?></td>
                                    <td><?= number_format($b['entitlement_days'], 1) ?></td>
                                    <td><?= number_format($b['used_days'], 1) ?></td>
                                    <td><?= number_format($b['pending_days'], 1) ?></td>
                                    <td><strong><?= number_format($b['remaining_days'], 1) ?></strong></td>
                                    <td>
                                        <form action="<?= base_url('administrador/lisensa/balansu/update/' . $b['id']) ?>" method="post" class="d-flex gap-2">
                                            <?= csrf_field() ?>
                                            <input type="number" name="entitlement_days" value="<?= esc($b['entitlement_days']) ?>" step="0.5" min="0" class="form-control form-control-sm" style="width: 90px;">
                                            <button type="submit" class="btn btn-primary btn-sm">Rai</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($balances)): ?>
                                <tr><td colspan="7" class="text-center text-muted">Seidauk iha balansu. Hanehan Jera.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
