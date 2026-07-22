<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3"><strong>Perfil</strong> Funsionáriu</h1>

<div class="row">
    <div class="col-md-4 col-xl-3">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Foto Perfil</h5>
            </div>
            <div class="card-body text-center">
                <?php if(!empty($funsionariu['foto_perfil'])): ?>
                    <img src="<?= base_url('uploads/perfil/'.$funsionariu['foto_perfil']) ?>" alt="<?= $funsionariu['naran_kompletu'] ?>" class="img-fluid rounded-circle mb-2" style="width: 128px; height: 128px; object-fit: cover;" />
                <?php else: ?>
                    <img src="<?= base_url('assets/images/avatar.png') ?>" alt="<?= $funsionariu['naran_kompletu'] ?>" class="img-fluid rounded-circle mb-2" style="width: 128px; height: 128px; object-fit: cover;" />
                <?php endif; ?>
                <h5 class="card-title mb-0"><?= $funsionariu['naran_kompletu'] ?></h5>
                <div class="text-muted mb-2"><?= $funsionariu['naran_pozisaun'] ?></div>

                <div class="mt-3">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalUpdateFoto">Atualiza Foto</button>
                </div>
            </div>
            <hr class="my-0" />
            <div class="card-body">
                <h5 class="h6 card-title">Kategoria</h5>
                <span class="badge bg-primary me-1 my-1"><?= $funsionariu['naran_kategoria'] ?></span>
            </div>
            <hr class="my-0" />
            <div class="card-body">
                <h5 class="h6 card-title">Diresaun</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-1"><i class="align-middle me-1" data-feather="briefcase"></i> <?= $funsionariu['naran_diresaun'] ?></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-8 col-xl-9">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informasaun Pesoál</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <th width="30%">NID</th>
                            <td><?= $funsionariu['nid'] ?></td>
                        </tr>
                        <tr>
                            <th>Naran Kompletu</th>
                            <td><?= $funsionariu['naran_kompletu'] ?></td>
                        </tr>
                        <tr>
                            <th>Seksu</th>
                            <td><?= $funsionariu['seksu'] ?></td>
                        </tr>
                        <tr>
                            <th>Fatin & Data Moris</th>
                            <td><?= $funsionariu['fatin_moris'] ?>, <?= !empty($funsionariu['data_moris']) ? date('d-m-Y', strtotime($funsionariu['data_moris'])) : '-' ?></td>
                        </tr>
                        <tr>
                            <th>Hela Fatin</th>
                            <td><?= $funsionariu['hela_fatin'] ?></td>
                        </tr>
                        <tr>
                            <th>Nu. Telefone</th>
                            <td><?= $funsionariu['nu_telefone'] ?></td>
                        </tr>
                        <tr>
                            <th>Estadu Sivil</th>
                            <td><?= $funsionariu['estadu_sivil'] ?></td>
                        </tr>
                        <tr>
                            <th>Data Hahu Servisu</th>
                            <td><?= !empty($funsionariu['data_hahu_servisu']) ? date('d-m-Y', strtotime($funsionariu['data_hahu_servisu'])) : '-' ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informasaun Akun Tama</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <th width="30%">Naran Utilizador</th>
                            <td><?= $funsionariu['naran_utilizador'] ?></td>
                        </tr>
                        <tr>
                            <th>Senha</th>
                            <td><button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalUpdatePassword">Troka Senha</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUpdateFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= base_url('funsionariu/perfil/foto') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Atualiza Foto Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Foto JPG/PNG maksimal 2MB</label>
                    <input type="file" name="foto_perfil" class="form-control" accept="image/png,image/jpeg" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kansela</button>
                    <button type="submit" class="btn btn-primary">Rai Foto</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalUpdatePassword" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= base_url('funsionariu/perfil/password') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Troka Senha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Senha Tuan</label>
                        <input type="password" name="password_lama" class="form-control" required autocomplete="current-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Senha Foun</label>
                        <input type="password" name="password_baru" class="form-control" required minlength="8" autocomplete="new-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirma Senha Foun</label>
                        <input type="password" name="password_konfirma" class="form-control" required minlength="8" autocomplete="new-password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kansela</button>
                    <button type="submit" class="btn btn-warning">Troka Senha</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>
