<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0"><strong>Jestaun</strong> Departamentu</h1>
        <p class="text-muted mb-0">Jere no organiza lista departamentu iha sistema.</p>
    </div>
</div>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background-color: #f0fdf4; color: #166534;">
        <div class="d-flex align-items-center">
            <i data-feather="check-circle" class="me-2" style="width: 18px;"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Taka"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background-color: #fef2f2; color: #991b1b;">
        <div class="d-flex align-items-center">
            <i data-feather="alert-octagon" class="me-2" style="width: 18px;"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Taka"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Aumenta Departamentu</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('administrador/departamentu') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-4">
                        <label class="form-label">Naran Departamentu</label>
                        <input type="text" name="naran_departamentu" class="form-control" placeholder="Eskrebe naran departamentu..." required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="align-middle me-1" data-feather="plus"></i> Submete
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">Lista Departamentu</h5>
                <span class="badge bg-primary-light text-primary"><?= count($departamentu) ?> Total</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 80px;">#</th>
                                <th>Naran Departamentu</th>
                                <th class="text-end" style="width: 200px;">Asaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($departamentu as $i => $d): ?>
                            <tr>
                                <td><span class="text-muted"><?= $i+1 ?></span></td>
                                <td class="font-medium text-dark"><?= esc($d['naran_departamentu']) ?></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $d['id'] ?>">
                                            <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                                        </button>
                                        <form action="<?= base_url('administrador/departamentu/delete/'.$d['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hamos departamentu ne\'e?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="modalEdit<?= $d['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content shadow-lg">
                                        <form action="<?= base_url('administrador/departamentu/update/'.$d['id']) ?>" method="post">
                    <?= csrf_field() ?>
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title font-medium">Edit Departamentu</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="mb-0">
                                                    <label class="form-label">Naran Departamentu</label>
                                                    <input type="text" name="naran_departamentu" class="form-control" value="<?= esc($d['naran_departamentu']) ?>" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top-0">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kansela</button>
                                                <button type="submit" class="btn btn-primary px-4">Atualiza</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php if(empty($departamentu)): ?>
                                <tr><td colspan="3" class="text-center py-4 text-muted">La iha dadus departamentu.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
