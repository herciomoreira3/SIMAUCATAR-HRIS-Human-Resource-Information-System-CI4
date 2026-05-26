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
                    <a class="btn btn-primary btn-sm" href="#">Update Foto</a>
                </div>
            </div>
            <hr class="my-0" />
            <div class="card-body">
                <h5 class="h6 card-title">Kategoria</h5>
                <span class="badge bg-primary me-1 my-1"><?= $funsionariu['naran_kategoria'] ?></span>
            </div>
            <hr class="my-0" />
            <div class="card-body">
                <h5 class="h6 card-title">Departamentu</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-1"><i class="align-middle me-1" data-feather="briefcase"></i> <?= $funsionariu['naran_departamentu'] ?></li>
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
                <h5 class="card-title mb-0">Informasaun Akun Login</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <th width="30%">Username</th>
                            <td><?= $funsionariu['naran_utilizador'] ?></td>
                        </tr>
                        <tr>
                            <th>Password</th>
                            <td><a href="#" class="btn btn-sm btn-warning">Konta Password Foun</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
