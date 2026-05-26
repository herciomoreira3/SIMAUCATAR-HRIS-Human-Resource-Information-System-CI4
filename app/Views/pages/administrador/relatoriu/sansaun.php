<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3">Relatóriu <strong>Sansaun</strong></h1>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Filtra Relatóriu</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('administrador/relatoriu/sansaun') ?>" method="get" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Fulan</label>
                        <select name="fulan" class="form-select">
                            <?php for($m=1; $m<=12; $m++): ?>
                                <option value="<?= $m ?>" <?= $filter['fulan'] == $m ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tinan</label>
                        <select name="tinan" class="form-select">
                            <?php for($y=date('Y'); $y>=date('Y')-5; $y--): ?>
                                <option value="<?= $y ?>" <?= $filter['tinan'] == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estadu Sansaun</label>
                        <select name="estadu" class="form-select">
                            <option value="">-- Hotu-hotu --</option>
                            <option value="Ativu" <?= $filter['estadu'] == 'Ativu' ? 'selected' : '' ?>>Ativu</option>
                            <option value="Konkluidu" <?= $filter['estadu'] == 'Konkluidu' ? 'selected' : '' ?>>Konkluidu</option>
                            <option value="Retira" <?= $filter['estadu'] == 'Retira' ? 'selected' : '' ?>>Retira</option>
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

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Lista Sansaun Funsionáriu</h5>
                <div class="d-flex gap-2">
                    <form action="<?= base_url('administrador/relatoriu/export/sansaun') ?>" method="post">
                        <input type="hidden" name="fulan" value="<?= $filter['fulan'] ?>">
                        <input type="hidden" name="tinan" value="<?= $filter['tinan'] ?>">
                        <input type="hidden" name="estadu" value="<?= $filter['estadu'] ?>">
                        <input type="hidden" name="export_type" value="pdf">
                        <button type="submit" class="btn btn-danger btn-sm"><i data-feather="file"></i> Exporta PDF</button>
                    </form>
                    <form action="<?= base_url('administrador/relatoriu/export/sansaun') ?>" method="post">
                        <input type="hidden" name="fulan" value="<?= $filter['fulan'] ?>">
                        <input type="hidden" name="tinan" value="<?= $filter['tinan'] ?>">
                        <input type="hidden" name="estadu" value="<?= $filter['estadu'] ?>">
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
                                <th>Tipu Sansaun</th>
                                <th>Data</th>
                                <th>Valor Total</th>
                                <th>Pagadu</th>
                                <th>Estadu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($sansaun as $s): ?>
                            <tr>
                                <td><?= $s['nid'] ?></td>
                                <td><?= $s['naran_kompletu'] ?></td>
                                <td><?= $s['naran_tipu'] ?></td>
                                <td><?= date('d-m-Y', strtotime($s['data_sansaun'])) ?></td>
                                <td>$<?= number_format($s['valor_total'], 2) ?></td>
                                <td>$<?= number_format($s['valor_pagadu'], 2) ?></td>
                                <td>
                                    <?php 
                                        $badge = 'secondary';
                                        if($s['estadu_sansaun'] == 'Ativu') $badge = 'danger';
                                        if($s['estadu_sansaun'] == 'Konkluidu') $badge = 'success';
                                        if($s['estadu_sansaun'] == 'Retira') $badge = 'info';
                                    ?>
                                    <span class="badge bg-<?= $badge ?>"><?= $s['estadu_sansaun'] ?></span>
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
