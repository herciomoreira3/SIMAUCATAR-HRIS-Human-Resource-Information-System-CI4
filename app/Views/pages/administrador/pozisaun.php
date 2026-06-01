<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3"><strong>Jestaun</strong> Pozisaun</h1>

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
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Aumenta Pozisaun</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('administrador/pozisaun') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Naran Pozisaun</label>
                        <input type="text" name="naran_pozisaun" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Saláriu Báziku ($)</label>
                        <input type="number" step="0.01" name="salariu_baziku" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submete</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Lista Pozisaun</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Naran Pozisaun</th>
                            <th>Saláriu Báziku</th>
                            <th>Asaun</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pozisaun as $i => $p): ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td><?= esc($p['naran_pozisaun']) ?></td>
                            <td>$ <?= number_format($p['salariu_baziku'], 2) ?></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $p['id'] ?>">Edit</button>
                                <form action="<?= base_url('administrador/pozisaun/delete/'.$p['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hamos pozisaun ne\'e?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-danger btn-sm">Hamos</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEdit<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="<?= base_url('administrador/pozisaun/update/'.$p['id']) ?>" method="post">
                    <?= csrf_field() ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Pozisaun</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Naran Pozisaun</label>
                                                <input type="text" name="naran_pozisaun" class="form-control" value="<?= esc($p['naran_pozisaun']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Saláriu Báziku ($)</label>
                                                <input type="number" step="0.01" name="salariu_baziku" class="form-control" value="<?= $p['salariu_baziku'] ?>" required>
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
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
