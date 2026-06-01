<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3"><strong>Pedidu</strong> Lisensa</h1>

<?php if (!empty($leave_balances)): ?>
<div class="row mb-3">
    <?php foreach ($leave_balances as $balance): ?>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small"><?= esc($balance['leave_type']) ?> <?= esc($balance['year']) ?></div>
                    <div class="h4 mb-0"><?= number_format($balance['remaining_days'], 1) ?> loron</div>
                    <div class="small text-muted">Usadu <?= number_format($balance['used_days'], 1) ?>, pendente <?= number_format($balance['pending_days'], 1) ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Hato'o Pedidu Foun</h5>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="alert-message">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Taka"></button>
                    </div>
                <?php endif; ?>
                <form action="<?= base_url('funsionariu/lisensa') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Tipu Lisensa</label>
                        <select name="tipu_lisensa" class="form-select" required>
                            <option value="Moras">Moras</option>
                            <option value="Anuál">Anuál</option>
                            <option value="Maternidade">Maternidade</option>
                            <option value="Lutu">Lutu</option>
                            <option value="Seluk">Seluk</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data Hahu</label>
                        <input type="date" name="data_hahu" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data Remata</label>
                        <input type="date" name="data_remata" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Razaun</label>
                        <textarea name="razaun" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dokumentu Suporta (Opsonál)</label>
                        <input type="file" name="dokumentu_suporta" class="form-control" accept="image/*,application/pdf">
                    </div>
                    <button type="submit" class="btn btn-primary">Submete Pedidu</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Ha'u-nia Lista Lisensa</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover my-0 datatable">
                        <thead>
                            <tr>
                                <th>Tipu</th>
                                <th>Data</th>
                                <th>Razaun</th>
                                <th>Estadu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($lisensa as $l): ?>
                            <tr>
                                <td><?= $l['tipu_lisensa'] ?></td>
                                <td>
                                    <?= date('d-m-Y', strtotime($l['data_hahu'])) ?> - <?= date('d-m-Y', strtotime($l['data_remata'])) ?>
                                </td>
                                <td><?= $l['razaun'] ?></td>
                                <td>
                                    <span class="badge bg-<?= $l['estadu_lisensa'] == 'Aprovadu' ? 'success' : ($l['estadu_lisensa'] == 'Pendente' ? 'warning' : 'danger') ?>">
                                        <?= $l['estadu_lisensa'] ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
