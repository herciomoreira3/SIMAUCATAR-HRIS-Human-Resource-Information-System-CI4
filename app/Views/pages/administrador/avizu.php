<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3"><strong>Jestaun</strong> Anunsiu</h1>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Lista Anunsiu Públiku</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAvizuModal">Aumenta Anunsiu</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover my-0 datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Títulu</th>
                                <th>Konteúdu</th>
                                <th>Data Publikasaun</th>
                                <th>Asaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach($avizu as $a): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($a['titulu']) ?></td>
                                <td><?= esc(substr(strip_tags($a['konteudu']), 0, 50) . (strlen(strip_tags($a['konteudu'])) > 50 ? '...' : '')) ?></td>
                                <td><?= date('d-m-Y', strtotime($a['data_publikasaun'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal<?= $a['id'] ?>">Detallu</button>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#expirationModal<?= $a['id'] ?>"><i class="align-middle" data-feather="settings"></i></button>
                                    <form action="<?= base_url('administrador/avizu/delete/'.$a['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hamos anunsiu ne\'e?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-danger">Hamos</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Expiration Modal -->
                            <div class="modal fade" id="expirationModal<?= $a['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="<?= base_url('administrador/avizu/expiration/'.$a['id']) ?>" method="post">
                    <?= csrf_field() ?>
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning text-dark">
                                                <h5 class="modal-title">Konfigurasaun Hamos Otomátiku</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Seta data no oras hodi hamos anunsiu ne'e automatikamente.</p>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Data</label>
                                                        <input type="date" name="data_remata" class="form-control" value="<?= !empty($a['data_remata']) ? date('Y-m-d', strtotime($a['data_remata'])) : '' ?>">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Oras</label>
                                                        <input type="time" name="time_remata" class="form-control" value="<?= !empty($a['data_remata']) ? date('H:i', strtotime($a['data_remata'])) : '' ?>">
                                                    </div>
                                                </div>
                                                <div class="alert alert-info">
                                                    Vazia deit se lakohi hamos automatikamente.
                                                </div>
                                                <?php if(!empty($a['data_remata'])): ?>
                                                    <p class="text-danger"><strong>Seta hela ba: <?= date('d-m-Y H:i', strtotime($a['data_remata'])) ?></strong></p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kansela</button>
                                                <button type="submit" class="btn btn-warning">Rai Konfigurasaun</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Detail Modal -->
                            <div class="modal fade" id="detailModal<?= $a['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title text-white">Detallu Anunsiu</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h4><?= esc($a['titulu']) ?></h4>
                                            <p class="text-muted"><small>Publika iha: <?= date('d-m-Y', strtotime($a['data_publikasaun'])) ?></small></p>
                                            <hr>
                                            <div style="white-space: pre-wrap;"><?= esc($a['konteudu']) ?></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Taka</button>
                                        </div>
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
</div>

<!-- Add Anunsiu Modal -->
<div class="modal fade" id="addAvizuModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="<?= base_url('administrador/avizu') ?>" method="post">
                    <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Aumenta Anunsiu Foun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Títulu Anunsiu</label>
                        <input type="text" name="titulu" class="form-control" required maxlength="150" placeholder="Ez: Anunsiu Feriadu, Reuniaun...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konteúdu Anunsiu</label>
                        <textarea name="konteudu" class="form-control" rows="10" required maxlength="5000" placeholder="Hakere konteúdu anunsiu iha ne'e..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kansela</button>
                    <button type="submit" class="btn btn-primary">Publika Anunsiu</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>
