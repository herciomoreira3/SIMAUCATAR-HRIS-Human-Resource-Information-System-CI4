<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3"><strong>Jestaun</strong> Prezensa</h1>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
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
                    <h6 class="mt-2"><strong>Sesion Dader</strong></h6>
                    <div class="mb-3">
                        <label class="form-label">Tama Hahu</label>
                        <input type="time" name="tama_hahu_dader" class="form-control"
                               value="<?= $settings['tama_hahu_dader'] ?? '08:00' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tama Remata</label>
                        <input type="time" name="tama_remata_dader" class="form-control"
                               value="<?= $settings['tama_remata_dader'] ?? '09:00' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sai Hahu</label>
                        <input type="time" name="sai_hahu_dader" class="form-control"
                               value="<?= $settings['sai_hahu_dader'] ?? '12:00' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sai Remata</label>
                        <input type="time" name="sai_remata_dader" class="form-control"
                               value="<?= $settings['sai_remata_dader'] ?? '13:00' ?>" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tama_manual_dader" id="tama_manual_dader" value="1"
                                   <?= (isset($settings['tama_manual_dader']) && $settings['tama_manual_dader'] == 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="tama_manual_dader">Permite Tama Dader iha tempu ruma</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="sai_manual_dader" id="sai_manual_dader" value="1"
                                   <?= (isset($settings['sai_manual_dader']) && $settings['sai_manual_dader'] == 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="sai_manual_dader">Permite Sai Dader iha tempu ruma</label>
                        </div>
                    </div>

                    <h6 class="mt-4"><strong>Sesion Lokraik</strong></h6>
                    <div class="mb-3">
                        <label class="form-label">Tama Hahu</label>
                        <input type="time" name="tama_hahu_lokraik" class="form-control"
                               value="<?= $settings['tama_hahu_lokraik'] ?? '14:00' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tama Remata</label>
                        <input type="time" name="tama_remata_lokraik" class="form-control"
                               value="<?= $settings['tama_remata_lokraik'] ?? '15:00' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sai Hahu</label>
                        <input type="time" name="sai_hahu_lokraik" class="form-control"
                               value="<?= $settings['sai_hahu_lokraik'] ?? '17:00' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sai Remata</label>
                        <input type="time" name="sai_remata_lokraik" class="form-control"
                               value="<?= $settings['sai_remata_lokraik'] ?? '18:00' ?>" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tama_manual_lokraik" id="tama_manual_lokraik" value="1"
                                   <?= (isset($settings['tama_manual_lokraik']) && $settings['tama_manual_lokraik'] == 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="tama_manual_lokraik">Permite Tama Lokraik iha tempu ruma</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="sai_manual_lokraik" id="sai_manual_lokraik" value="1"
                                   <?= (isset($settings['sai_manual_lokraik']) && $settings['sai_manual_lokraik'] == 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="sai_manual_lokraik">Permite Sai Lokraik iha tempu ruma</label>
                        </div>
                    </div>

                    <h6 class="mt-4"><strong>Jeral</strong></h6>
                    <div class="mb-3">
                        <label class="form-label d-block">Loron Servisu Estra (Weekend)</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="sabadu" id="sabadu" value="1"
                                   <?= (isset($settings['sabadu']) && $settings['sabadu'] == 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="sabadu">Sábadu</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="domingu" id="domingu" value="1"
                                   <?= (isset($settings['domingu']) && $settings['domingu'] == 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="domingu">Domingu</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Atualiza Konfigurasaun</button>
                </form>
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
                                <th>Tama Dader</th>
                                <th>Sai Dader</th>
                                <th>Tama Lokraik</th>
                                <th>Sai Lokraik</th>
                                <th>Estadu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($prezensa as $p): ?>
                            <tr>
                                <td><?= $p['nid'] ?? '' ?></td>
                                <td><?= $p['naran_kompletu'] ?? '' ?></td>
                                <td data-sort="<?= $p['data_prezensa'] ?>"><?= date('d-m-Y', strtotime($p['data_prezensa'])) ?></td>
                                <td><?= $p['oras_tama_dader'] ?? '-' ?></td>
                                <td><?= $p['oras_sai_dader'] ?? '-' ?></td>
                                <td><?= $p['oras_tama_lokraik'] ?? '-' ?></td>
                                <td><?= $p['oras_sai_lokraik'] ?? '-' ?></td>
                                <td>
                                    <?php
                                        $badgeClass = 'secondary';
                                        $status = $p['estadu_prezensa'];
                                        if ($status == 'Prezente') $badgeClass = 'success';
                                        elseif ($status == 'Loron Sorin') $badgeClass = 'warning';
                                        elseif ($status == 'Falta') $badgeClass = 'danger';
                                        elseif ($status == 'Lisensa') $badgeClass = 'info';
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
