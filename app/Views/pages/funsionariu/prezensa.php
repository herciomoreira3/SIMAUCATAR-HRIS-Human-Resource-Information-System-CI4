<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3"><strong>Prezensa</strong> Loron-loron</h1>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Clock In / Clock Out</h5>
            </div>
            <div class="card-body text-center">
                <p class="mb-2">Data Ohin: <strong><?= date('d-m-Y') ?></strong></p>
                <p class="mb-4">Oras Agora: <strong id="realtime-clock"><?= date('H:i:s') ?></strong></p>
                
                <?php 
                    $now = date('H:i:s');
                    $hahu_tama = $settings['tama_hahu'] ?? '00:00:00';
                    $remata_tama = $settings['tama_remata'] ?? '23:59:59';
                    $hahu_sai = $settings['sai_hahu'] ?? '00:00:00';
                    $remata_sai = $settings['sai_remata'] ?? '23:59:59';

                    $tama_active = (strtotime($now) >= strtotime($hahu_tama) && strtotime($now) <= strtotime($remata_tama));
                    $sai_active = (strtotime($now) >= strtotime($hahu_sai) && strtotime($now) <= strtotime($remata_sai));
                ?>

                <div class="d-grid gap-2">
                    <?php if (!$prezensa_ohin): ?>
                        <div class="alert alert-warning small">
                            Horáriu Tama: <?= date('H:i', strtotime($hahu_tama)) ?> - <?= date('H:i', strtotime($remata_tama)) ?>
                        </div>
                        <form action="<?= base_url('funsionariu/prezensa/tama') ?>" method="post">
                            <button type="submit" class="btn btn-primary btn-lg w-100 py-4" <?= !$tama_active ? 'disabled' : '' ?>>
                                <i class="align-middle me-2" data-feather="log-in"></i> TAMA (Clock In)
                            </button>
                        </form>
                        <?php if (!$tama_active): ?>
                            <p class="text-danger small mt-2">Butaun Tama sei deit ativa iha oras ne'ebé konese.</p>
                        <?php endif; ?>

                    <?php elseif ($prezensa_ohin['oras_tama'] && empty($prezensa_ohin['oras_sai'])): ?>
                        <div class="alert alert-success">
                            Ita tama ona iha oras: <strong><?= $prezensa_ohin['oras_tama'] ?></strong>
                        </div>
                        <div class="alert alert-warning small">
                            Horáriu Sai: <?= date('H:i', strtotime($hahu_sai)) ?> - <?= date('H:i', strtotime($remata_sai)) ?>
                        </div>
                        <form action="<?= base_url('funsionariu/prezensa/sai') ?>" method="post">
                            <button type="submit" class="btn btn-danger btn-lg w-100 py-4" <?= !$sai_active ? 'disabled' : '' ?>>
                                <i class="align-middle me-2" data-feather="log-out"></i> SAI (Clock Out)
                            </button>
                        </form>
                        <?php if (!$sai_active): ?>
                            <p class="text-danger small mt-2">Butaun Sai sei deit ativa iha oras ne'ebé konese.</p>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="alert alert-info py-4">
                            <i class="align-middle me-2" data-feather="check-circle"></i>
                            Ita finaliza ona prezensa ba loron ohin. <br>
                            Tama: <strong><?= $prezensa_ohin['oras_tama'] ?></strong> | 
                            Sai: <strong><?= $prezensa_ohin['oras_sai'] ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
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
                                <th>Tama</th>
                                <th>Sai</th>
                                <th>Estadu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($istoria_prezensa as $p): ?>
                            <tr>
                                <td data-sort="<?= $p['data_prezensa'] ?>"><?= date('d-m-Y', strtotime($p['data_prezensa'])) ?></td>
                                <td><?= $p['oras_tama'] ?? '-' ?></td>
                                <td><?= $p['oras_sai'] ?? '-' ?></td>
                                <td>
                                    <?php 
                                        $badge = 'secondary';
                                        $label = $p['estadu_prezensa'];
                                        $now = date('H:i:s');
                                        $ohin = date('Y-m-d');
                                        $remata_sai = $settings['sai_remata'] ?? '23:59:59';
                                        
                                        if($p['estadu_prezensa'] == 'Prezente' || $p['estadu_prezensa'] == 'Tardi') {
                                            if(empty($p['oras_sai'])) {
                                                // Check if it's past the checkout window
                                                if($p['data_prezensa'] < $ohin || ($p['data_prezensa'] == $ohin && strtotime($now) > strtotime($remata_sai))) {
                                                    $badge = 'danger';
                                                    $label = 'Falta (La Sai)';
                                                } else {
                                                    $badge = 'warning';
                                                    $label = 'Prosesu Hela';
                                                }
                                            } else {
                                                $badge = 'success';
                                                $label = 'Prezente';
                                            }
                                        } elseif($p['estadu_prezensa'] == 'Falta') {
                                            $badge = 'danger';
                                        } elseif($p['estadu_prezensa'] == 'Lisensa') {
                                            $badge = 'info';
                                        }
                                    ?>
                                    <span class="badge bg-<?= $badge ?>">
                                        <?= $label ?>
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
