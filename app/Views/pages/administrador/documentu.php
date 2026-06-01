<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-3"><strong>Jestaun</strong> Dokumentu</h1>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Hatama Dokumentu</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('administrador/documentu/upload') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Funsionariu</label>
                        <select name="funsionariu_id" class="form-select" required>
                            <option value="">Hili funsionariu</option>
                            <?php foreach ($funsionariu as $f): ?>
                                <option value="<?= $f['id'] ?>"><?= esc($f['nid']) ?> - <?= esc($f['naran_kompletu']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategoria</label>
                        <select name="category" class="form-select" required>
                            <?php foreach (($categories ?? []) as $category): ?>
                                <option value="<?= esc($category['name']) ?>"><?= esc($category['name']) ?></option>
                            <?php endforeach; ?>
                            <?php if (empty($categories)): ?>
                                <option value="Dokumentu">Dokumentu</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Visibilidade</label>
                        <select name="visibility" class="form-select" required>
                            <option value="admin_only">Admin deit</option>
                            <option value="employee_visible">Funsionariu bele haree</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fail</label>
                        <input type="file" name="documentu" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Hatama</button>
                </form>
                <hr>
                <form action="<?= base_url('administrador/documentu/category') ?>" method="post" class="d-flex gap-2">
                    <?= csrf_field() ?>
                    <input type="text" name="name" class="form-control" placeholder="Kategoria foun" maxlength="100" required>
                    <button type="submit" class="btn btn-secondary">Aumenta</button>
                </form>
                <?php if (!empty($categories)): ?>
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <?php foreach ($categories as $category): ?>
                            <form action="<?= base_url('administrador/documentu/category/delete/' . $category['id']) ?>" method="post" onsubmit="return confirm('Hamos kategoria nee?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-outline-secondary btn-sm"><?= esc($category['name']) ?> x</button>
                            </form>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Lista Dokumentu</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th>Funsionariu</th>
                                <th>Kategoria</th>
                                <th>Fail</th>
                                <th>Visibilidade</th>
                                <th>Data</th>
                                <th>Asaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($documents as $doc): ?>
                                <tr>
                                    <td><?= esc($doc['nid'] ?? '-') ?> - <?= esc($doc['naran_kompletu'] ?? '-') ?></td>
                                    <td><?= esc($doc['category']) ?></td>
                                    <td>
                                        <a href="<?= base_url('uploads/documentu/' . $doc['stored_name']) ?>" target="_blank" rel="noopener">
                                            <?= esc($doc['original_name']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $doc['visibility'] === 'employee_visible' ? 'success' : 'secondary' ?>">
                                            <?= $doc['visibility'] === 'employee_visible' ? 'Funsionariu' : 'Admin' ?>
                                        </span>
                                    </td>
                                    <td><?= !empty($doc['created_at']) ? date('d-m-Y', strtotime($doc['created_at'])) : '-' ?></td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#previewDocument<?= $doc['id'] ?>">Haree</button>
                                        <form action="<?= base_url('administrador/documentu/delete/' . $doc['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hamos dokumentu ne\'e?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-danger btn-sm">Hamos</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($documents)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">La iha dokumentu.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
