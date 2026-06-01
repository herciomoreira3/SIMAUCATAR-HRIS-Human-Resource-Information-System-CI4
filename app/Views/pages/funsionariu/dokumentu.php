<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-3"><strong>Ha'u-nia</strong> Dokumentu</h1>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Lista Dokumentu</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Kategoria</th>
                        <th>Fail</th>
                        <th>Data Hatama</th>
                        <th>Asaun</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $doc): ?>
                        <tr>
                            <td><?= esc($doc['category']) ?></td>
                            <td><?= esc($doc['original_name']) ?></td>
                            <td><?= !empty($doc['created_at']) ? date('d-m-Y', strtotime($doc['created_at'])) : '-' ?></td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#previewDocument<?= $doc['id'] ?>">Haree</button>
                                <a href="<?= base_url('uploads/documentu/' . $doc['stored_name']) ?>" target="_blank" rel="noopener" class="btn btn-primary btn-sm">Hatun</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($documents)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">La iha dokumentu disponivel.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php foreach ($documents as $doc): ?>
    <div class="modal fade" id="previewDocument<?= $doc['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= esc($doc['original_name']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                </div>
                <div class="modal-body">
                    <?php if (str_starts_with($doc['mime_type'], 'image/')): ?>
                        <img src="<?= base_url('uploads/documentu/' . $doc['stored_name']) ?>" class="img-fluid rounded border" alt="<?= esc($doc['original_name']) ?>">
                    <?php else: ?>
                        <iframe src="<?= base_url('uploads/documentu/' . $doc['stored_name']) ?>" style="width: 100%; height: 70vh;" class="border rounded"></iframe>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?= $this->endSection(); ?>
