<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3">Relatóriu <strong>Saláriu</strong></h1>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Filtra Relatóriu</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('administrador/relatoriu/salariu') ?>" method="get" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Fulan</label>
                        <select name="fulan" class="form-select">
                            <?php for($m=1; $m<=12; $m++): ?>
                                <option value="<?= $m ?>" <?= $filter['fulan'] == $m ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tinan</label>
                        <select name="tinan" class="form-select">
                            <?php for($y=date('Y'); $y>=date('Y')-5; $y--): ?>
                                <option value="<?= $y ?>" <?= $filter['tinan'] == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filtra Relatóriu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Rekapitulasaun Pagamentu Saláriu</h5>
                <div class="d-flex gap-2">
                    <form action="<?= base_url('administrador/relatoriu/export/salariu') ?>" method="post">
                    <?= csrf_field() ?>
                        <input type="hidden" name="fulan" value="<?= $filter['fulan'] ?>">
                        <input type="hidden" name="tinan" value="<?= $filter['tinan'] ?>">
                        <input type="hidden" name="export_type" value="pdf">
                        <button type="submit" class="btn btn-danger btn-sm"><i data-feather="file"></i> Exporta PDF</button>
                    </form>
                    <form action="<?= base_url('administrador/relatoriu/export/salariu') ?>" method="post">
                    <?= csrf_field() ?>
                        <input type="hidden" name="fulan" value="<?= $filter['fulan'] ?>">
                        <input type="hidden" name="tinan" value="<?= $filter['tinan'] ?>">
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
                                <th>Saláriu Báziku</th>
                                <th>Total Subsídiu</th>
                                <th>Total Deskontu</th>
                                <th>Saláriu Líkuidu</th>
                                <th>Data Pagamentu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($salariu as $s): ?>
                            <tr>
                                <td><?= $s['nid'] ?></td>
                                <td><?= $s['naran_kompletu'] ?></td>
                                <td>$<?= number_format($s['salariu_baziku'], 2) ?></td>
                                <td>$<?= number_format($s['total_subsidiu'], 2) ?></td>
                                <td class="text-danger">$<?= number_format($s['total_deskontu'], 2) ?></td>
                                <td class="fw-bold">$<?= number_format($s['salariu_liquidu'], 2) ?></td>
                                <td><?= date('d-m-Y', strtotime($s['data_pagamentu'])) ?></td>
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
