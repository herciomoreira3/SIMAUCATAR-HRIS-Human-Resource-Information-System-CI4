<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3"><strong>Prezensa</strong> Loron-loron</h1>

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
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Absénsia Loron Ohin</h5>
            </div>
            <div class="card-body text-center">
                <p class="mb-2">Data: <strong><?= date('d-m-Y') ?></strong></p>
                <p class="mb-4">Oras: <strong id="realtime-clock"><?= date('H:i:s') ?></strong></p>

                <!-- Dader Section -->
                <div class="mb-4">
                    <h6 class="text-center"><strong>Sesion Dader</strong></h6>
                    <div class="d-grid gap-2 mt-2">
                        <form action="<?= base_url('funsionariu/prezensa/tama_dader') ?>" method="post">
                            <?= csrf_field() ?>
                            <?php
                            $tamaDaderActive = (isset($settings['tama_manual_dader']) && $settings['tama_manual_dader'] == 1) ||
                                (strtotime(date('H:i:s')) >= strtotime($settings['tama_hahu_dader']) && strtotime(date('H:i:s')) <= strtotime($settings['tama_remata_dader']));
                            $hasTamaDader = $prezensa_ohin && !empty($prezensa_ohin['oras_tama_dader']);
                            ?>
                            <button type="submit" class="btn btn-primary btn-lg w-100"
                                    <?= (!$tamaDaderActive || $hasTamaDader) ? 'disabled' : '' ?>>
                                <i class="align-middle me-2" data-feather="log-in"></i> Tama Dader
                                <?php if ($hasTamaDader): ?> (<?= $prezensa_ohin['oras_tama_dader'] ?>) <?php endif; ?>
                            </button>
                        </form>
                        <form action="<?= base_url('funsionariu/prezensa/sai_dader') ?>" method="post">
                            <?= csrf_field() ?>
                            <?php
                            $saiDaderActive = (isset($settings['sai_manual_dader']) && $settings['sai_manual_dader'] == 1) ||
                                (strtotime(date('H:i:s')) >= strtotime($settings['sai_hahu_dader']) && strtotime(date('H:i:s')) <= strtotime($settings['sai_remata_dader']));
                            $hasSaiDader = $prezensa_ohin && !empty($prezensa_ohin['oras_sai_dader']);
                            ?>
                            <button type="submit" class="btn btn-danger btn-lg w-100"
                                    <?= (!$saiDaderActive || $hasSaiDader) ? 'disabled' : '' ?>>
                                <i class="align-middle me-2" data-feather="log-out"></i> Sai Dader
                                <?php if ($hasSaiDader): ?> (<?= $prezensa_ohin['oras_sai_dader'] ?>) <?php endif; ?>
                            </button>
                        </form>
                    </div>
                    <div class="text-muted small mt-2">
                        Tama: <?= date('H:i', strtotime($settings['tama_hahu_dader'])) ?> - <?= date('H:i', strtotime($settings['tama_remata_dader'])) ?><br>
                        Sai: <?= date('H:i', strtotime($settings['sai_hahu_dader'])) ?> - <?= date('H:i', strtotime($settings['sai_remata_dader'])) ?>
                    </div>
                </div>

                <!-- Lokraik Section -->
                <div class="mb-4">
                    <h6 class="text-center"><strong>Sesion Lokraik</strong></h6>
                    <div class="d-grid gap-2 mt-2">
                        <form action="<?= base_url('funsionariu/prezensa/tama_lokraik') ?>" method="post">
                            <?= csrf_field() ?>
                            <?php
                            $tamaLokraikActive = (isset($settings['tama_manual_lokraik']) && $settings['tama_manual_lokraik'] == 1) ||
                                (strtotime(date('H:i:s')) >= strtotime($settings['tama_hahu_lokraik']) && strtotime(date('H:i:s')) <= strtotime($settings['tama_remata_lokraik']));
                            $hasTamaLokraik = $prezensa_ohin && !empty($prezensa_ohin['oras_tama_lokraik']);
                            ?>
                            <button type="submit" class="btn btn-primary btn-lg w-100"
                                    <?= (!$tamaLokraikActive || $hasTamaLokraik) ? 'disabled' : '' ?>>
                                <i class="align-middle me-2" data-feather="log-in"></i> Tama Lokraik
                                <?php if ($hasTamaLokraik): ?> (<?= $prezensa_ohin['oras_tama_lokraik'] ?>) <?php endif; ?>
                            </button>
                        </form>
                        <form action="<?= base_url('funsionariu/prezensa/sai_lokraik') ?>" method="post">
                            <?= csrf_field() ?>
                            <?php
                            $saiLokraikActive = (isset($settings['sai_manual_lokraik']) && $settings['sai_manual_lokraik'] == 1) ||
                                (strtotime(date('H:i:s')) >= strtotime($settings['sai_hahu_lokraik']) && strtotime(date('H:i:s')) <= strtotime($settings['sai_remata_lokraik']));
                            $hasSaiLokraik = $prezensa_ohin && !empty($prezensa_ohin['oras_sai_lokraik']);
                            ?>
                            <button type="submit" class="btn btn-danger btn-lg w-100"
                                    <?= (!$saiLokraikActive || $hasSaiLokraik) ? 'disabled' : '' ?>>
                                <i class="align-middle me-2" data-feather="log-out"></i> Sai Lokraik
                                <?php if ($hasSaiLokraik): ?> (<?= $prezensa_ohin['oras_sai_lokraik'] ?>) <?php endif; ?>
                            </button>
                        </form>
                    </div>
                    <div class="text-muted small mt-2">
                        Tama: <?= date('H:i', strtotime($settings['tama_hahu_lokraik'])) ?> - <?= date('H:i', strtotime($settings['tama_remata_lokraik'])) ?><br>
                        Sai: <?= date('H:i', strtotime($settings['sai_hahu_lokraik'])) ?> - <?= date('H:i', strtotime($settings['sai_remata_lokraik'])) ?>
                    </div>
                </div>

                <!-- Status -->
                <?php if ($prezensa_ohin): ?>
                    <div class="alert alert-info mt-2">
                        Estadu Prezensa Ohin:
                        <span class="badge
                            <?= $prezensa_ohin['estadu_prezensa'] == 'Prezente' ? 'bg-success' : ($prezensa_ohin['estadu_prezensa'] == 'Loron Sorin' ? 'bg-warning' :
                            ($prezensa_ohin['estadu_prezensa'] == 'Falta' ? 'bg-danger' :
                            ($prezensa_ohin['estadu_prezensa'] == 'Lisensa' ? 'bg-info' : 'bg-secondary'))) ?>">
                            <?= $prezensa_ohin['estadu_prezensa'] ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Istória Prezensa</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-striped datatable" data-order='[[0, "desc"]]'>
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Tama Dader</th>
                                <th>Sai Dader</th>
                                <th>Tama Lokraik</th>
                                <th>Sai Lokraik</th>
                                <th>Estadu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($istoria_prezensa as $p): ?>
                            <tr>
                                <td data-sort="<?= $p['data_prezensa'] ?>"><?= date('d-m-Y', strtotime($p['data_prezensa'])) ?></td>
                                <td><?= $p['oras_tama_dader'] ?? '-' ?></td>
                                <td><?= $p['oras_sai_dader'] ?? '-' ?></td>
                                <td><?= $p['oras_tama_lokraik'] ?? '-' ?></td>
                                <td><?= $p['oras_sai_lokraik'] ?? '-' ?></td>
                                <td>
                                    <?php
                                        $badge = 'secondary';
                                        $status = $p['estadu_prezensa'];
                                        if ($status == 'Prezente') {
                                            $badge = 'success';
                                        } elseif ($status == 'Loron Sorin') {
                                            $badge = 'warning';
                                        } elseif ($status == 'Falta') {
                                            $badge = 'danger';
                                        } elseif ($status == 'Lisensa') {
                                            $badge = 'info';
                                        }
                                    ?>
                                    <span class="badge bg-<?= $badge ?>">
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

<script>
    function updateClock() {
        const now = new Date();
        const timeString = now.getHours().toString().padStart(2, '0') + ':' +
                           now.getMinutes().toString().padStart(2, '0') + ':' +
                           now.getSeconds().toString().padStart(2, '0');
        document.getElementById('realtime-clock').textContent = timeString;
    }
    setInterval(updateClock, 1000);
</script>

<?= $this->endSection(); ?>
