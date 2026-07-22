<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3"><strong>Jestaun</strong> Lisensa</h1>

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

<div class="row mb-3">
    <div class="col-12 d-flex justify-content-end">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#kriaLisensaModal">
            <i data-feather="plus-circle"></i> Kria Lisensa ba Funsionáriu
        </button>
    </div>
</div>

<!-- Modal: Kria Lisensa husi Admin -->
<div class="modal fade" id="kriaLisensaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="<?= base_url('administrador/lisensa/kria') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kria Lisensa ba Funsionáriu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="alert alert-info py-2 mb-3">
                        <i data-feather="info" class="align-middle me-1"></i>
                        Lisensa ne'ebé kria husi Admin sei automátikamente <strong>Aprovadu</strong> no la presiza pedidu husi funsionáriu.
                        Uza funsionalidade ne'e ba Viajem, Enkontru, ka atividade ofisiál seluk.
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Funsionáriu <span class="text-danger">*</span></label>
                            <select name="funsionariu_id" class="form-select" required>
                                <option value="">-- Hili Funsionáriu --</option>
                                <?php foreach ($funsionariu as $f): ?>
                                    <option value="<?= $f['id'] ?>"><?= esc($f['naran_kompletu']) ?> (<?= esc($f['nid']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipu Lisensa <span class="text-danger">*</span></label>
                            <select name="tipu_lisensa" class="form-select" required>
                                <option value="">-- Hili Tipu --</option>
                                <?php foreach ($tipu_lisensa as $tl): ?>
                                    <option value="<?= esc($tl['naran_tipu']) ?>"><?= esc($tl['naran_tipu']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sesaun <span class="text-danger">*</span></label>
                            <select name="sesaun" id="adminSesaunSelect" class="form-select" required onchange="adminHandleSesaun(this)">
                                <option value="Loron Tomak">Loron Tomak</option>
                                <option value="Dader">Dader</option>
                                <option value="Lokraik">Lokraik</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Data Hahu <span class="text-danger">*</span></label>
                            <input type="date" name="data_hahu" id="adminDataHahu" class="form-control" required onchange="adminSyncDataRemata()">
                        </div>
                        <div class="col-md-4" id="adminDataRemataWrapper">
                            <label class="form-label">Data Remata <span class="text-danger">*</span></label>
                            <input type="date" name="data_remata" id="adminDataRemata" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Razaun / Objetivu <span class="text-danger">*</span></label>
                            <textarea name="razaun" class="form-control" rows="3" required
                                placeholder="Eskreve razaun ka objetivu lisensa ne'e (ex: Viajem ba konferénsia, Enkontru ofisiál iha...)"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Taka</button>
                    <button type="submit" class="btn btn-success">
                        <i data-feather="check-circle"></i> Kria no Aprova Lisensa
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function adminHandleSesaun(sel) {
    const wrapper = document.getElementById('adminDataRemataWrapper');
    const remata = document.getElementById('adminDataRemata');
    const hahu = document.getElementById('adminDataHahu');
    if (sel.value === 'Dader' || sel.value === 'Lokraik') {
        remata.value = hahu.value;
        remata.readOnly = true;
        wrapper.style.opacity = '0.6';
    } else {
        remata.readOnly = false;
        wrapper.style.opacity = '1';
    }
}
function adminSyncDataRemata() {
    const sel = document.getElementById('adminSesaunSelect');
    const hahu = document.getElementById('adminDataHahu');
    const remata = document.getElementById('adminDataRemata');
    if (!remata.value || remata.value < hahu.value) {
        remata.value = hahu.value;
    }
    if (sel.value === 'Dader' || sel.value === 'Lokraik') {
        remata.value = hahu.value;
    }
}
</script>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Lista Pedidu Lisensa</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover my-0 datatable">
                        <thead>
                            <tr>
                                <th>NID</th>
                                <th>Funsionáriu</th>
                                <th>Tipu</th>
                                <th>Sesaun</th>
                                <th>Data</th>
                                <th>Razaun</th>
                                <th>Estadu</th>
                                <th>Asaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($lisensa as $l): ?>
                            <tr>
                                <td><?= $l['nid'] ?></td>
                                <td><?= $l['naran_kompletu'] ?></td>
                                <td><?= $l['tipu_lisensa'] ?></td>
                                <td>
                                    <?php
                                    $sesaun = $l['sesaun'] ?? 'Loron Tomak';
                                    $sb = $sesaun === 'Dader' ? 'warning' : ($sesaun === 'Lokraik' ? 'info' : 'secondary');
                                    ?>
                                    <span class="badge bg-<?= $sb ?>"><?= $sesaun ?></span>
                                </td>
                                <td>
                                    <?= date('d-m-Y', strtotime($l['data_hahu'])) ?>
                                    <?= ($l['data_hahu'] !== $l['data_remata']) ? '- ' . date('d-m-Y', strtotime($l['data_remata'])) : '' ?>
                                </td>
                                <td><?= $l['razaun'] ?></td>
                                <td>
                                    <span class="badge bg-<?= $l['estadu_lisensa'] == 'Aprovadu' ? 'success' : ($l['estadu_lisensa'] == 'Pendente' ? 'warning' : 'danger') ?>">
                                        <?= $l['estadu_lisensa'] ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#reviewModal<?= $l['id'] ?>">Review</button>

                                    <!-- Modal Review -->
                                    <div class="modal fade" id="reviewModal<?= $l['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form action="<?= base_url('administrador/lisensa/aprova/'.$l['id']) ?>" method="post">
                    <?= csrf_field() ?>
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Review Pedidu Lisensa</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <p><strong>Funsionáriu:</strong> <?= $l['naran_kompletu'] ?></p>
                                                        <p><strong>Tipu:</strong> <?= $l['tipu_lisensa'] ?></p>
                                                        <p><strong>Sesaun:</strong>
                                                            <?php
                                                            $sesaun = $l['sesaun'] ?? 'Loron Tomak';
                                                            $sb = $sesaun === 'Dader' ? 'warning' : ($sesaun === 'Lokraik' ? 'info' : 'secondary');
                                                            ?>
                                                            <span class="badge bg-<?= $sb ?>"><?= $sesaun ?></span>
                                                        </p>
                                                        <p><strong>Data:</strong> <?= date('d-m-Y', strtotime($l['data_hahu'])) ?><?= ($l['data_hahu'] !== $l['data_remata']) ? ' - ' . date('d-m-Y', strtotime($l['data_remata'])) : '' ?></p>
                                                        <p><strong>Razaun:</strong> <?= $l['razaun'] ?></p>
                                                        
                                                        <?php if(!empty($l['dokumentu_suporta'])): ?>
                                                            <p><strong>Dokumentu Suporta:</strong></p>
                                                            <a href="<?= base_url('uploads/lisensa/'.$l['dokumentu_suporta']) ?>" target="_blank">
                                                                <img src="<?= base_url('uploads/lisensa/'.$l['dokumentu_suporta']) ?>" class="img-fluid rounded border mb-3" style="max-height: 200px;">
                                                            </a>
                                                        <?php else: ?>
                                                            <p><em>Dokumentu suporta la iha.</em></p>
                                                        <?php endif; ?>

                                                        <div class="mb-3">
                                                            <label class="form-label text-dark">Komentáriu Admin (Obrigatóriu)</label>
                                                            <textarea name="komentariu_admin" class="form-control" rows="3" required placeholder="Fó razaun aprova ka rezeita..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" name="estadu_lisensa" value="Aprovadu" class="btn btn-success">Aprova</button>
                                                        <button type="submit" name="estadu_lisensa" value="Rezeitadu" class="btn btn-danger">Rezeita</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
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
