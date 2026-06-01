<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3"><strong>Jestaun</strong> Kategoria</h1>

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
                <h5 class="card-title mb-0">Aumenta Kategoria</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('administrador/kategoria') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Naran Kategoria</label>
                        <input type="text" name="naran_kategoria" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submete</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Lista Kategoria</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Naran Kategoria</th>
                            <th>Asaun</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($kategoria as $i => $k): ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td><?= esc($k['naran_kategoria']) ?></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $k['id'] ?>">Edit</button>
                                <form action="<?= base_url('administrador/kategoria/delete/'.$k['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hamos kategoria ne\'e?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-danger btn-sm">Hamos</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEdit<?= $k['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="<?= base_url('administrador/kategoria/update/'.$k['id']) ?>" method="post">
                    <?= csrf_field() ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Kategoria</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Naran Kategoria</label>
                                                <input type="text" name="naran_kategoria" class="form-control" value="<?= esc($k['naran_kategoria']) ?>" required>
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
