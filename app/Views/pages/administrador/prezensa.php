<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3"><strong>Jestaun</strong> Prezensa</h1>

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
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Konfigurasaun Absénsia</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('administrador/prezensa/settings') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Check-In Hahu (Tama)</label>
                        <input type="time" name="tama_hahu" class="form-control" value="<?= $settings['tama_hahu'] ?? '08:00' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Check-In Remata (Tama)</label>
                        <input type="time" name="tama_remata" class="form-control" value="<?= $settings['tama_remata'] ?? '09:00' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Check-Out Hahu (Sai)</label>
                        <input type="time" name="sai_hahu" class="form-control" value="<?= $settings['sai_hahu'] ?? '17:00' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Check-Out Remata (Sai)</label>
                        <input type="time" name="sai_remata" class="form-control" value="<?= $settings['sai_remata'] ?? '18:00' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Loron Servisu Estra (Weekend)</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="sabadu" id="sabadu" value="1" <?= (isset($settings['sabadu']) && $settings['sabadu'] == 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="sabadu">Sábadu</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="domingu" id="domingu" value="1" <?= (isset($settings['domingu']) && $settings['domingu'] == 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="domingu">Domingu</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Toleransia Tardi (minutu)</label>
                        <input type="number" min="0" max="240" name="toleransia_minutu" class="form-control" value="<?= $settings['toleransia_minutu'] ?? 15 ?>" required>
                    </div>

                    
                    <button type="submit" class="btn btn-primary w-100">Atualiza Konfigurasaun</button>
                </form>
                <div class="mt-3 small text-muted">
                    <i class="align-middle" data-feather="info"></i> 
                    Prosesu mark absent agora dijalankan lewat command <code>php spark attendance:mark-absent</code>, bukan setiap halaman dibuka.
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Lista Prezensa Funsionáriu</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover my-0 datatable" data-order='[[2, "desc"]]'>
                        <thead>
                            <tr>
                                <th>NID</th>
                                <th>Funsionáriu</th>
                                <th>Data</th>
                                <th>Tama</th>
                                <th>Sai</th>
                                <th>Estadu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($prezensa as $p): ?>
                            <tr>
                                <td><?= $p['nid'] ?? '' ?></td>
                                <td><?= $p['naran_kompletu'] ?? '' ?></td>
                                <td data-sort="<?= $p['data_prezensa'] ?>"><?= date('d-m-Y', strtotime($p['data_prezensa'])) ?></td>
                                <td><?= $p['oras_tama'] ?? '-' ?></td>
                                <td><?= $p['oras_sai'] ?? '-' ?></td>
                                <td>
                                    <?php 
                                        $badgeClass = 'secondary';
                                        $status = $p['estadu_prezensa'];
                                        if ($status == 'Prezente') $badgeClass = 'success';
                                        elseif ($status == 'Tardi') $badgeClass = 'warning';
                                        elseif ($status == 'Lisensa') $badgeClass = 'info';
                                        elseif ($status == 'Falta') $badgeClass = 'danger';
                                        elseif ($status == 'Incomplete') $badgeClass = 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>">
                                        <?= $status ?>
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
