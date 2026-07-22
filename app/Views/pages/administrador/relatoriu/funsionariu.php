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
                        <label class="form-label">Pozisaun</label>
                        <select name="pozisaun_id" class="form-select">
                            <option value="">-- Hotu-hotu --</option>
                            <?php foreach($pozisaun as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= $filter['pozisaun_id'] == $p['id'] ? 'selected' : '' ?>><?= $p['naran_pozisaun'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kategoria</label>
                        <select name="kategoria_id" class="form-select">
                            <option value="">-- Hotu-hotu --</option>
                            <?php foreach($kategoria as $k): ?>
                                <option value="<?= $k['id'] ?>" <?= $filter['kategoria_id'] == $k['id'] ? 'selected' : '' ?>><?= $k['naran_kategoria'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Grau</label>
                        <select name="grau_id" class="form-select">
                            <option value="">-- Hotu-hotu --</option>
                            <?php foreach($grau as $g): ?>
                                <option value="<?= $g['id'] ?>" <?= $filter['grau_id'] == $g['id'] ? 'selected' : '' ?>><?= $g['naran_grau'] ?></option>
                            <?php endforeach; ?>
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

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Lista Funsionáriu</h5>
                <div class="d-flex gap-2">
                    <form action="<?= base_url('administrador/relatoriu/export/funsionariu') ?>" method="post">
                    <?= csrf_field() ?>
                        <input type="hidden" name="diresaun_id" value="<?= $filter['diresaun_id'] ?>">
                        <input type="hidden" name="pozisaun_id" value="<?= $filter['pozisaun_id'] ?>">
                        <input type="hidden" name="kategoria_id" value="<?= $filter['kategoria_id'] ?>">
                        <input type="hidden" name="grau_id" value="<?= $filter['grau_id'] ?>">
                        <input type="hidden" name="export_type" value="pdf">
                        <button type="submit" class="btn btn-danger btn-sm"><i data-feather="file"></i> Exporta PDF</button>
                    </form>
                    <form action="<?= base_url('administrador/relatoriu/export/funsionariu') ?>" method="post">
                    <?= csrf_field() ?>
                        <input type="hidden" name="diresaun_id" value="<?= $filter['diresaun_id'] ?>">
                        <input type="hidden" name="pozisaun_id" value="<?= $filter['pozisaun_id'] ?>">
                        <input type="hidden" name="kategoria_id" value="<?= $filter['kategoria_id'] ?>">
                        <input type="hidden" name="grau_id" value="<?= $filter['grau_id'] ?>">
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
                                <th>Diresaun</th>
                                <th>Pozisaun</th>
                                <th>Kategoria</th>
                                <th>Grau</th>
                                <th>Data Hahu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($funsionariu as $f): ?>
                            <tr>
                                <td><?= $f['nid'] ?></td>
                                <td><?= $f['naran_kompletu'] ?></td>
                                <td><?= $f['seksu'] ?></td>
                                <td><?= $f['naran_diresaun'] ?></td>
                                <td><?= $f['naran_pozisaun'] ?></td>
                                <td><?= $f['naran_kategoria'] ?></td>
                                <td><?= $f['naran_grau'] ?? '-' ?></td>
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
