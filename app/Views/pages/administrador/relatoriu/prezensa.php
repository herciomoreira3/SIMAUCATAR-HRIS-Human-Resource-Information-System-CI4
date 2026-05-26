<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3">Relatóriu <strong>Prezensa</strong></h1>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                        <label class="form-label">Departamentu</label>
                        <select name="departamentu_id" class="form-select">
                            <option value="">-- Hotu-hotu --</option>
                            <?php foreach($departamentu as $d): ?>
                                <option value="<?= $d['id'] ?>" <?= $filter['departamentu_id'] == $d['id'] ? 'selected' : '' ?>><?= $d['naran_departamentu'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filtra Relatóriu</button>
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
            <div class="card-body">
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
                        <input type="hidden" name="data_hahu" value="<?= $filter['data_hahu'] ?>">
                        <input type="hidden" name="data_remata" value="<?= $filter['data_remata'] ?>">
                        <input type="hidden" name="departamentu_id" value="<?= $filter['departamentu_id'] ?>">
                        <input type="hidden" name="export_type" value="pdf">
                        <button type="submit" class="btn btn-danger btn-sm"><i data-feather="file"></i> Exporta PDF</button>
                    </form>
                    <form action="<?= base_url('administrador/relatoriu/export/prezensa') ?>" method="post" class="d-inline">
                        <input type="hidden" name="data_hahu" value="<?= $filter['data_hahu'] ?>">
                        <input type="hidden" name="data_remata" value="<?= $filter['data_remata'] ?>">
                        <input type="hidden" name="departamentu_id" value="<?= $filter['departamentu_id'] ?>">
                        <input type="hidden" name="export_type" value="excel">
                        <button type="submit" class="btn btn-success btn-sm"><i data-feather="grid"></i> Exporta Excel</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover datatable">
                        <thead>
                            <tr>
                                <th>NID</th>
                                <th>Funsionáriu</th>
                                <th>Departamentu</th>
                                <th class="text-center text-success">Prezente</th>
                                <th class="text-center text-danger">Falta</th>
                                <th class="text-center text-info">Lisensa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $grand_prezente = 0; $grand_falta = 0; $grand_lisensa = 0;
                            foreach($prezensa as $p): 
                                $grand_prezente += $p['total_prezente'];
                                $grand_falta += $p['total_falta'];
                                $grand_lisensa += $p['total_lisensa'];
                            ?>
                            <tr>
                                <td><?= $p['nid'] ?></td>
                                <td><?= $p['naran_kompletu'] ?></td>
                                <td><?= $p['naran_departamentu'] ?></td>
                                <td class="text-center"><?= $p['total_prezente'] ?></td>
                                <td class="text-center"><?= $p['total_falta'] ?></td>
                                <td class="text-center"><?= $p['total_lisensa'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
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
                data: [<?= $grand_prezente ?>, <?= $grand_falta ?>, <?= $grand_lisensa ?>]
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
            colors: ['#28a745', '#dc3545', '#17a2b8'],
            dataLabels: {
                enabled: true
            },
            xaxis: {
                categories: ['Prezente', 'Falta', 'Lisensa'],
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
