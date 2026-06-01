<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3"><strong>Jestaun</strong> Funsionáriu</h1>

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
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Lista Funsionáriu 
                    <button type="button" class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#modalAddFunsionariu">Aumenta Funsionáriu</button>
                    <button type="button" class="btn btn-secondary btn-sm float-end me-2" data-bs-toggle="modal" data-bs-target="#modalImportFunsionariu">Importa CSV</button>
                    <a href="<?= base_url('administrador/funsionariu/template') ?>" class="btn btn-outline-secondary btn-sm float-end me-2">Modelu</a>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover my-0 datatable">
                        <thead>
                            <tr>
                                <th>NID</th>
                                <th>Naran Kompletu</th>
                                <th>Departamentu</th>
                                <th>Pozisaun</th>
                                <th>Estadu</th>
                                <th>Asaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($funsionariu as $f): ?>
                            <tr>
                                <td><?= $f['nid'] ?></td>
                                <td><?= $f['naran_kompletu'] ?></td>
                                <td><?= $f['naran_departamentu'] ?></td>
                                <td><?= $f['naran_pozisaun'] ?></td>
                                <td>
                                    <span class="badge bg-<?= isset($f['estadu_kontu']) && $f['estadu_kontu'] == 'Ativu' ? 'success' : 'primary' ?>">
                                        <?= $f['estadu_kontu'] ?? 'Ativu' ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $f['id'] ?>">Detallu</button>
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalUpdate<?= $f['id'] ?>">Atualiza</button>
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalResetPassword<?= $f['id'] ?>">Troka Senha</button>
                                    <form action="<?= base_url('administrador/funsionariu/delete/'.$f['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hamos funsionariu <?= $f['naran_kompletu'] ?> no nia akun utilizador?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-danger btn-sm">Hamos</button>
                                    </form>
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

<div class="modal fade" id="modalImportFunsionariu" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('administrador/funsionariu/import') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Importa Funsionariu CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Fail CSV</label>
                        <input type="file" name="file_import" class="form-control" accept=".csv,.txt" required>
                    </div>
                    <a href="<?= base_url('administrador/funsionariu/template') ?>" class="btn btn-outline-secondary btn-sm">Hatun Modelu</a>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kansela</button>
                    <button type="submit" class="btn btn-primary">Importa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach($funsionariu as $f): ?>
<!-- Modal Detail -->
<div class="modal fade" id="modalDetail<?= $f['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detallu Funsionáriu: <?= $f['naran_kompletu'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <?php if(!empty($f['foto_perfil'])): ?>
                            <img src="<?= base_url('uploads/perfil/'.$f['foto_perfil']) ?>" class="img-fluid rounded shadow" style="max-height: 200px; width: 100%; object-fit: cover;">
                        <?php else: ?>
                            <img src="<?= base_url('assets/images/avatar.png') ?>" class="img-fluid rounded shadow" style="max-height: 200px;">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-sm">
                            <tr><th width="35%">NID</th><td>: <?= $f['nid'] ?></td></tr>
                            <tr><th>Naran Kompletu</th><td>: <?= $f['naran_kompletu'] ?></td></tr>
                            <tr><th>Seksu</th><td>: <?= $f['seksu'] ?></td></tr>
                            <tr><th>Fatin & Data Moris</th><td>: <?= $f['fatin_moris'] ?>, <?= !empty($f['data_moris']) ? date('d-m-Y', strtotime($f['data_moris'])) : '-' ?></td></tr>
                            <tr><th>Hela Fatin</th><td>: <?= $f['hela_fatin'] ?></td></tr>
                            <tr><th>Estadu Sivil</th><td>: <?= $f['estadu_sivil'] ?></td></tr>
                            <tr><th>Nu. Telefone</th><td>: <?= $f['nu_telefone'] ?></td></tr>
                            <tr><th>Departamentu</th><td>: <?= $f['naran_departamentu'] ?></td></tr>
                            <tr><th>Pozisaun</th><td>: <?= $f['naran_pozisaun'] ?></td></tr>
                            <tr><th>Kategoria</th><td>: <?= $f['naran_kategoria'] ?></td></tr>
                            <tr><th>Data Hahu Servisu</th><td>: <?= !empty($f['data_hahu_servisu']) ? date('d-m-Y', strtotime($f['data_hahu_servisu'])) : '-' ?></td></tr>
                            <tr><th>Naran Utilizador</th><td>: <?= $f['naran_utilizador'] ?></td></tr>
                            <tr><th>Estadu Akun</th><td>: <span class="badge bg-success"><?= $f['estadu_kontu'] ?? 'Ativu' ?></span></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalResetPassword<?= $f['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('administrador/funsionariu/reset-password/'.$f['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Troka Senha: <?= esc($f['naran_kompletu']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Senha Foun</label>
                        <input type="password" name="password_baru" class="form-control" minlength="8" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirma Senha</label>
                        <input type="password" name="password_konfirma" class="form-control" minlength="8" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kansela</button>
                    <button type="submit" class="btn btn-primary">Troka</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Update -->
<div class="modal fade" id="modalUpdate<?= $f['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('administrador/funsionariu/update/'.$f['id']) ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Atualiza Dadus Funsionáriu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">NID</label>
                            <input type="text" name="nid" class="form-control" value="<?= $f['nid'] ?>" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Naran Kompletu</label>
                            <input type="text" name="naran_kompletu" class="form-control" value="<?= $f['naran_kompletu'] ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Seksu</label>
                            <select name="seksu" class="form-select" required>
                                <option value="Mane" <?= $f['seksu'] == 'Mane' ? 'selected' : '' ?>>Mane</option>
                                <option value="Feto" <?= $f['seksu'] == 'Feto' ? 'selected' : '' ?>>Feto</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fatin Moris</label>
                            <input type="text" name="fatin_moris" class="form-control" value="<?= $f['fatin_moris'] ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Data Moris</label>
                            <input type="date" name="data_moris" class="form-control" value="<?= $f['data_moris'] ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hela Fatin</label>
                            <input type="text" name="hela_fatin" class="form-control" value="<?= $f['hela_fatin'] ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estadu Sivil</label>
                            <select name="estadu_sivil" class="form-select" required>
                                <option value="Solteiru" <?= $f['estadu_sivil'] == 'Solteiru' ? 'selected' : '' ?>>Solteiru</option>
                                <option value="Kaben Nain" <?= $f['estadu_sivil'] == 'Kaben Nain' ? 'selected' : '' ?>>Kaben Nain</option>
                                <option value="Divorsiadu" <?= $f['estadu_sivil'] == 'Divorsiadu' ? 'selected' : '' ?>>Divorsiadu</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nu. Telefone</label>
                            <input type="text" name="nu_telefone" class="form-control" value="<?= $f['nu_telefone'] ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Data Hahu Servisu</label>
                            <input type="date" name="data_hahu_servisu" class="form-control" value="<?= $f['data_hahu_servisu'] ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Departamentu</label>
                            <select name="departamentu_id" class="form-select" required>
                                <?php foreach($departamentu as $d): ?>
                                <option value="<?= $d['id'] ?>" <?= $f['departamentu_id'] == $d['id'] ? 'selected' : '' ?>><?= $d['naran_departamentu'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Pozisaun</label>
                            <select name="pozisaun_id" class="form-select" required>
                                <?php foreach($pozisaun as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= $f['pozisaun_id'] == $p['id'] ? 'selected' : '' ?>><?= $p['naran_pozisaun'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kategoria</label>
                            <select name="kategoria_id" class="form-select" required>
                                <?php foreach($kategoria as $k): ?>
                                <option value="<?= $k['id'] ?>" <?= $f['kategoria_id'] == $k['id'] ? 'selected' : '' ?>><?= $k['naran_kategoria'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Foto Perfil (Vazia se lakohi troka)</label>
                            <input type="file" name="foto_perfil" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12 mb-3">
                            <hr>
                            <h6>Informasaun Akun Tama</h6>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Naran Utilizador</label>
                            <input type="text" name="username" class="form-control" value="<?= $f['naran_utilizador'] ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Senha (husik mamuk se lakohi troka)</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Papel</label>
                            <select name="papel_id" class="form-select" required>
                                <?php foreach($papel as $pl): ?>
                                <option value="<?= $pl['id'] ?>" <?= $f['role_id'] == $pl['id'] ? 'selected' : '' ?>><?= $pl['role_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kansela</button>
                    <button type="submit" class="btn btn-primary">Atualiza</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Modal Add Funsionariu -->
<div class="modal fade" id="modalAddFunsionariu" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('administrador/funsionariu') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Formuláriu Funsionáriu Foun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">NID</label>
                            <input type="text" name="nid" class="form-control" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Naran Kompletu</label>
                            <input type="text" name="naran_kompletu" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Seksu</label>
                            <select name="seksu" class="form-select" required>
                                <option value="Mane">Mane</option>
                                <option value="Feto">Feto</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fatin Moris</label>
                            <input type="text" name="fatin_moris" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Data Moris</label>
                            <input type="date" name="data_moris" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hela Fatin</label>
                            <input type="text" name="hela_fatin" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estadu Sivil</label>
                            <select name="estadu_sivil" class="form-select" required>
                                <option value="Solteiru">Solteiru</option>
                                <option value="Kaben Nain">Kaben Nain</option>
                                <option value="Divorsiadu">Divorsiadu</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nu. Telefone</label>
                            <input type="text" name="nu_telefone" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Data Hahu Servisu</label>
                            <input type="date" name="data_hahu_servisu" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Departamentu</label>
                            <select name="departamentu_id" class="form-select" required>
                                <?php foreach($departamentu as $d): ?>
                                <option value="<?= $d['id'] ?>"><?= $d['naran_departamentu'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Pozisaun</label>
                            <select name="pozisaun_id" class="form-select" required>
                                <?php foreach($pozisaun as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= $p['naran_pozisaun'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kategoria</label>
                            <select name="kategoria_id" class="form-select" required>
                                <?php foreach($kategoria as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= $k['naran_kategoria'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Foto Perfil</label>
                            <input type="file" name="foto_perfil" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12 mb-3">
                            <hr>
                            <h6>Informasaun Akun Tama</h6>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Naran Utilizador</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Papel</label>
                            <select name="papel_id" class="form-select" required>
                                <?php foreach($papel as $pl): ?>
                                <option value="<?= $pl['id'] ?>"><?= $pl['role_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kansela</button>
                    <button type="submit" class="btn btn-primary">Submete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
