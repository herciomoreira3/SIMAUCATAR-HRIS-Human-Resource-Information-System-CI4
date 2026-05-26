<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3"><strong>Jestaun</strong> Lisensa</h1>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Lista Pedidu Lisensa</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover my-0 datatable">
                        <thead>
                            <tr>
                                <th>NID</th>
                                <th>Funsionáriu</th>
                                <th>Tipu</th>
                                <th>Data</th>
                                <th>Razaun</th>
                                <th>Estadu</th>
                                <th>Asaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($lisensa as $l): ?>
                            <tr>
                                <td><?= $l['nid'] ?></td>
                                <td><?= $l['naran_kompletu'] ?></td>
                                <td><?= $l['tipu_lisensa'] ?></td>
                                <td>
                                    <?= date('d-m-Y', strtotime($l['data_hahu'])) ?> - <?= date('d-m-Y', strtotime($l['data_remata'])) ?>
                                </td>
                                <td><?= $l['razaun'] ?></td>
                                <td>
                                    <span class="badge bg-<?= $l['estadu_lisensa'] == 'Aprovadu' ? 'success' : ($l['estadu_lisensa'] == 'Pendente' ? 'warning' : 'danger') ?>">
                                        <?= $l['estadu_lisensa'] ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#reviewModal<?= $l['id'] ?>">Review</button>

                                    <!-- Modal Review -->
                                    <div class="modal fade" id="reviewModal<?= $l['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form action="<?= base_url('administrador/lisensa/aprova/'.$l['id']) ?>" method="post">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Review Pedidu Lisensa</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <p><strong>Funsionáriu:</strong> <?= $l['naran_kompletu'] ?></p>
                                                        <p><strong>Tipu:</strong> <?= $l['tipu_lisensa'] ?></p>
                                                        <p><strong>Data:</strong> <?= date('d-m-Y', strtotime($l['data_hahu'])) ?> - <?= date('d-m-Y', strtotime($l['data_remata'])) ?></p>
                                                        <p><strong>Razaun:</strong> <?= $l['razaun'] ?></p>
                                                        
                                                        <?php if(!empty($l['dokumentu_suporta'])): ?>
                                                            <p><strong>Dokumentu Suporta:</strong></p>
                                                            <a href="<?= base_url('uploads/lisensa/'.$l['dokumentu_suporta']) ?>" target="_blank">
                                                                <img src="<?= base_url('uploads/lisensa/'.$l['dokumentu_suporta']) ?>" class="img-fluid rounded border mb-3" style="max-height: 200px;">
                                                            </a>
                                                        <?php else: ?>
                                                            <p><em>Dokumentu suporta la iha.</em></p>
                                                        <?php endif; ?>

                                                        <div class="mb-3">
                                                            <label class="form-label text-dark">Komentáriu Admin (Obrigatóriu)</label>
                                                            <textarea name="komentariu_admin" class="form-control" rows="3" required placeholder="Fó razaun aprova ka rezeita..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" name="estadu_lisensa" value="Aprovadu" class="btn btn-success">Aprova</button>
                                                        <button type="submit" name="estadu_lisensa" value="Rezeitadu" class="btn btn-danger">Rezeita</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
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
