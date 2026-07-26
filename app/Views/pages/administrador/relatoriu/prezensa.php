<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3">Relatóriu <strong>Prezensa</strong></h1>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Taka"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Filtra Relatóriu</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('administrador/relatoriu/prezensa') ?>" method="get" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Data Hahu</label>
                        <input type="date" name="data_hahu" class="form-control" value="<?= $filter['data_hahu'] ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Data Remata</label>
                        <input type="date" name="data_remata" class="form-control" value="<?= $filter['data_remata'] ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Diresaun</label>
                        <select name="diresaun_id" class="form-select">
                            <option value="">-- Hotu-hotu --</option>
                            <?php foreach($diresaun as $d): ?>
                                <option value="<?= $d['id'] ?>" <?= $filter['diresaun_id'] == $d['id'] ? 'selected' : '' ?>><?= $d['naran_diresaun'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estadu Prezensa</label>
                        <select name="estadu" class="form-select">
                            <option value="">-- Hotu-hotu --</option>
                            <option value="Prezente" <?= ($filter['estadu'] ?? '') === 'Prezente' ? 'selected' : '' ?>>Prezente</option>
                            <option value="Loron Sorin" <?= ($filter['estadu'] ?? '') === 'Loron Sorin' ? 'selected' : '' ?>>Loron Sorin</option>
                            <option value="Falta" <?= ($filter['estadu'] ?? '') === 'Falta' ? 'selected' : '' ?>>Falta</option>
                            <option value="Lisensa" <?= ($filter['estadu'] ?? '') === 'Lisensa' ? 'selected' : '' ?>>Lisensa</option>
                            <option value="Incomplete" <?= ($filter['estadu'] ?? '') === 'Incomplete' ? 'selected' : '' ?>>Incomplete</option>
                        </select>
                    </div>
                    <div class="col-md-12 d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary px-4">Filtra Relatóriu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Visual Chart Summary -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="body p-3">
                <div id="chartPrezensa"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Dadus Rekapitulasaun</h5>
                <div>
                    <form action="<?= base_url('administrador/relatoriu/export/prezensa') ?>" method="post" class="d-inline">
                    <?= csrf_field() ?>
                        <input type="hidden" name="data_hahu" value="<?= $filter['data_hahu'] ?>">
                        <input type="hidden" name="data_remata" value="<?= $filter['data_remata'] ?>">
                        <input type="hidden" name="diresaun_id" value="<?= $filter['diresaun_id'] ?>">
                        <input type="hidden" name="estadu" value="<?= $filter['estadu'] ?? '' ?>">
                        <input type="hidden" name="export_type" value="pdf">
                        <button type="submit" class="btn btn-danger btn-sm"><i data-feather="file"></i> Exporta PDF</button>
                    </form>
                    <form action="<?= base_url('administrador/relatoriu/export/prezensa') ?>" method="post" class="d-inline">
                    <?= csrf_field() ?>
                        <input type="hidden" name="data_hahu" value="<?= $filter['data_hahu'] ?>">
                        <input type="hidden" name="data_remata" value="<?= $filter['data_remata'] ?>">
                        <input type="hidden" name="diresaun_id" value="<?= $filter['diresaun_id'] ?>">
                        <input type="hidden" name="estadu" value="<?= $filter['estadu'] ?? '' ?>">
                        <input type="hidden" name="export_type" value="excel">
                        <button type="submit" class="btn btn-success btn-sm"><i data-feather="grid"></i> Exporta Excel</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>NID</th>
                                <th>Funsionáriu</th>
                                <th>Diresaun</th>
                                <th class="text-center text-success">Prezente</th>
                                <th class="text-center text-warning">Loron Sorin</th>
                                <th class="text-center text-danger">Falta</th>
                                <th class="text-center text-info">Lisensa</th>
                                <th class="text-center text-secondary">Incomplete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $grand_prezente = 0; $grand_loron_sorin = 0; $grand_falta = 0; $grand_lisensa = 0; $grand_incomplete = 0;
                            foreach($prezensa as $p): 
                                $grand_prezente += $p['total_prezente'];
                                $grand_loron_sorin += $p['total_loron_sorin'];
                                $grand_falta += $p['total_falta'];
                                $grand_lisensa += $p['total_lisensa'];
                                $grand_incomplete += $p['total_incomplete'];
                            ?>
                            <tr>
                                <td><?= $p['nid'] ?></td>
                                <td><?= $p['naran_kompletu'] ?></td>
                                <td><?= $p['naran_diresaun'] ?></td>
                                <td class="text-center"><?= $p['total_prezente'] ?></td>
                                <td class="text-center"><?= $p['total_loron_sorin'] ?></td>
                                <td class="text-center"><?= $p['total_falta'] ?></td>
                                <td class="text-center"><?= $p['total_lisensa'] ?></td>
                                <td class="text-center"><?= $p['total_incomplete'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
                <?= view('pages/administrador/relatoriu/_pagination', ['pagination' => $pagination]) ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var options = {
            series: [{
                name: 'Total Loron',
                data: [<?= $grand_prezente ?>, <?= $grand_loron_sorin ?>, <?= $grand_falta ?>, <?= $grand_lisensa ?>, <?= $grand_incomplete ?>]
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: false,
                    distributed: true,
                }
            },
            colors: ['#28a745', '#f59e0b', '#dc3545', '#17a2b8', '#6c757d'],
            dataLabels: {
                enabled: true
            },
            xaxis: {
                categories: ['Prezente', 'Loron Sorin', 'Falta', 'Lisensa', 'Incomplete'],
            },

            title: {
                text: 'Rezumu Dezempeñu Prezensa (Periodu Seleksionadu)',
                align: 'center'
            }
        };

        var chart = new ApexCharts(document.querySelector("#chartPrezensa"), options);
        chart.render();
    });
</script>

<?= $this->endSection(); ?>
