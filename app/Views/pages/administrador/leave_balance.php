<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-3"><strong>Balansu</strong> Lisensa</h1>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Taka"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Taka"></button>
    </div>
<?php endif; ?>

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
                        <select name="leave_type" class="form-select" required>
                            <?php foreach ($tipu_lisensa as $tl): ?>
                                <option value="<?= esc($tl['naran_tipu']) ?>" <?= $tl['naran_tipu'] === 'Anuál' ? 'selected' : '' ?>><?= esc($tl['naran_tipu']) ?></option>
                            <?php endforeach; ?>
                        </select>
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

<div class="row mt-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Aumenta Tipu Lisensa</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('administrador/lisensa/tipu') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Naran Tipu Lisensa</label>
                        <input type="text" name="naran_tipu" class="form-control" placeholder="Eskrebe naran tipu lisensa..." required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Aumenta</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Lista Tipu Lisensa</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Naran Tipu Lisensa</th>
                                <th>Asaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tipu_lisensa as $i => $tl): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= esc($tl['naran_tipu']) ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editTipuModal<?= $tl['id'] ?>">Edit</button>
                                        <form action="<?= base_url('administrador/lisensa/tipu/delete/' . $tl['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('⚠️ ATENSAUN!\n\nHamos tipu lisensa: \"<?= esc(addslashes($tl['naran_tipu'])) ?>\"?\n\nAksaun ida ne\'e sei hamos mós:\n- Balansu lisensa hotu-hotu funsionáriu ne\'ebé iha tipu ne\'e\n- Pedidu lisensa hotu-hotu ne\'ebé uza tipu ne\'e\n\nDadus la bele fila fali. Konfirma?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-danger btn-sm">Hamos</button>
                                        </form>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editTipuModal<?= $tl['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form action="<?= base_url('administrador/lisensa/tipu/update/' . $tl['id']) ?>" method="post">
                                                    <?= csrf_field() ?>
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-dark">Edit Tipu Lisensa</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                                                        </div>
                                                        <div class="modal-body text-start p-3">
                                                            <div class="mb-3">
                                                                <label class="form-label text-dark">Naran Tipu Lisensa</label>
                                                                <input type="text" name="naran_tipu" value="<?= esc($tl['naran_tipu']) ?>" class="form-control text-dark" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Taka</button>
                                                            <button type="submit" class="btn btn-primary">Atualiza</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($tipu_lisensa)): ?>
                                <tr><td colspan="3" class="text-center text-muted">Seidauk iha tipu lisensa.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
