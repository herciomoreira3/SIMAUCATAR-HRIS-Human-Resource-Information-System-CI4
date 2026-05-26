<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3">Relatóriu <strong>Lisensa</strong></h1>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Filtra Relatóriu</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('administrador/relatoriu/lisensa') ?>" method="get" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Data Hahu</label>
                        <input type="date" name="data_hahu" class="form-control" value="<?= $filter['data_hahu'] ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Data Remata</label>
                        <input type="date" name="data_remata" class="form-control" value="<?= $filter['data_remata'] ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estadu Lisensa</label>
                        <select name="estadu" class="form-select">
                            <option value="">-- Hotu-hotu --</option>
                            <option value="Pendente" <?= $filter['estadu'] == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                            <option value="Aprovadu" <?= $filter['estadu'] == 'Aprovadu' ? 'selected' : '' ?>>Aprovadu</option>
                            <option value="Rejeitadu" <?= $filter['estadu'] == 'Rejeitadu' ? 'selected' : '' ?>>Rejeitadu</option>
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
                <h5 class="card-title mb-0">Lista Pedidu Lisensa</h5>
                <div class="d-flex gap-2">
                    <form action="<?= base_url('administrador/relatoriu/export/lisensa') ?>" method="post">
                        <input type="hidden" name="data_hahu" value="<?= $filter['data_hahu'] ?>">
                        <input type="hidden" name="data_remata" value="<?= $filter['data_remata'] ?>">
                        <input type="hidden" name="estadu" value="<?= $filter['estadu'] ?>">
                        <input type="hidden" name="export_type" value="pdf">
                        <button type="submit" class="btn btn-danger btn-sm"><i data-feather="file"></i> Exporta PDF</button>
                    </form>
                    <form action="<?= base_url('administrador/relatoriu/export/lisensa') ?>" method="post">
                        <input type="hidden" name="data_hahu" value="<?= $filter['data_hahu'] ?>">
                        <input type="hidden" name="data_remata" value="<?= $filter['data_remata'] ?>">
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
                                <th>Tipu Lisensa</th>
                                <th>Hahu</th>
                                <th>Remata</th>
                                <th>Estadu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($lisensa as $l): ?>
                            <tr>
                                <td><?= $l['nid'] ?></td>
                                <td><?= $l['naran_kompletu'] ?></td>
                                <td><?= $l['tipu_lisensa'] ?></td>
                                <td><?= date('d-m-Y', strtotime($l['data_hahu'])) ?></td>
                                <td><?= date('d-m-Y', strtotime($l['data_remata'])) ?></td>
                                <td>
                                    <?php 
                                        $badge = 'secondary';
                                        if($l['estadu_lisensa'] == 'Aprovadu') $badge = 'success';
                                        if($l['estadu_lisensa'] == 'Pendente') $badge = 'warning';
                                        if($l['estadu_lisensa'] == 'Rejeitadu') $badge = 'danger';
                                    ?>
                                    <span class="badge bg-<?= $badge ?>"><?= $l['estadu_lisensa'] ?></span>
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
