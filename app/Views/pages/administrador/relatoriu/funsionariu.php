<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3">Relatóriu <strong>Funsionáriu</strong></h1>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Filtra Relatóriu</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('administrador/relatoriu/funsionariu') ?>" method="get" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Departamentu</label>
                        <select name="departamentu_id" class="form-select">
                            <option value="">-- Hotu-hotu --</option>
                            <?php foreach($departamentu as $d): ?>
                                <option value="<?= $d['id'] ?>" <?= $filter['departamentu_id'] == $d['id'] ? 'selected' : '' ?>><?= $d['naran_departamentu'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pozisaun</label>
                        <select name="pozisaun_id" class="form-select">
                            <option value="">-- Hotu-hotu --</option>
                            <?php foreach($pozisaun as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= $filter['pozisaun_id'] == $p['id'] ? 'selected' : '' ?>><?= $p['naran_pozisaun'] ?></option>
                            <?php endforeach; ?>
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
                <h5 class="card-title mb-0">Lista Funsionáriu</h5>
                <div class="d-flex gap-2">
                    <form action="<?= base_url('administrador/relatoriu/export/funsionariu') ?>" method="post">
                        <input type="hidden" name="departamentu_id" value="<?= $filter['departamentu_id'] ?>">
                        <input type="hidden" name="pozisaun_id" value="<?= $filter['pozisaun_id'] ?>">
                        <input type="hidden" name="export_type" value="pdf">
                        <button type="submit" class="btn btn-danger btn-sm"><i data-feather="file"></i> Exporta PDF</button>
                    </form>
                    <form action="<?= base_url('administrador/relatoriu/export/funsionariu') ?>" method="post">
                        <input type="hidden" name="departamentu_id" value="<?= $filter['departamentu_id'] ?>">
                        <input type="hidden" name="pozisaun_id" value="<?= $filter['pozisaun_id'] ?>">
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
                                <th>Naran Kompletu</th>
                                <th>Seksu</th>
                                <th>Departamentu</th>
                                <th>Pozisaun</th>
                                <th>Kategoria</th>
                                <th>Data Hahu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($funsionariu as $f): ?>
                            <tr>
                                <td><?= $f['nid'] ?></td>
                                <td><?= $f['naran_kompletu'] ?></td>
                                <td><?= $f['seksu'] ?></td>
                                <td><?= $f['naran_departamentu'] ?></td>
                                <td><?= $f['naran_pozisaun'] ?></td>
                                <td><?= $f['naran_kategoria'] ?></td>
                                <td><?= date('d-m-Y', strtotime($f['data_hahu_servisu'])) ?></td>
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
