<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-3"><strong>Jestaun</strong> Feriadu</h1>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Aumenta Feriadu</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('administrador/feriadu') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Data</label>
                        <input type="date" name="holiday_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Titulu</label>
                        <input type="text" name="title" class="form-control" maxlength="150" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskrisaun</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Rai Feriadu</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Lista Feriadu</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Titulu</th>
                                <th>Deskrisaun</th>
                                <th>Asaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($holidays as $holiday): ?>
                                <tr>
                                    <td><?= date('d-m-Y', strtotime($holiday['holiday_date'])) ?></td>
                                    <td><?= esc($holiday['title']) ?></td>
                                    <td><?= esc($holiday['description'] ?? '-') ?></td>
                                    <td>
                                        <form action="<?= base_url('administrador/feriadu/delete/' . $holiday['id']) ?>" method="post" onsubmit="return confirm('Hamos feriadu nee?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-danger btn-sm">Hamos</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($holidays)): ?>
                                <tr><td colspan="4" class="text-center text-muted">La iha feriadu.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
