<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3"><strong>Jestaun</strong> Avizu</h1>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Lista Avizu Públiku</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAvizuModal">Aumenta Avizu</button>
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
                                <td><?= $a['titulu'] ?></td>
                                <td><?= substr($a['konteudu'], 0, 50) . (strlen($a['konteudu']) > 50 ? '...' : '') ?></td>
                                <td><?= date('d-m-Y', strtotime($a['data_publikasaun'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal<?= $a['id'] ?>">Detallu</button>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#expirationModal<?= $a['id'] ?>"><i class="align-middle" data-feather="settings"></i></button>
                                    <a href="<?= base_url('administrador/avizu/delete/'.$a['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hamos avizu ne\'e?')">Hamos</a>
                                </td>
                            </tr>

                            <!-- Expiration Modal -->
                            <div class="modal fade" id="expirationModal<?= $a['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="<?= base_url('administrador/avizu/expiration/'.$a['id']) ?>" method="post">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning text-dark">
                                                <h5 class="modal-title">Konfigurasaun Hamos Otomátiku</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Seta data no oras hodi hamos avizu ne'e automatikamente.</p>
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
                                            <h5 class="modal-title text-white">Detallu Avizu</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h4><?= $a['titulu'] ?></h4>
                                            <p class="text-muted"><small>Publika iha: <?= date('d-m-Y', strtotime($a['data_publikasaun'])) ?></small></p>
                                            <hr>
                                            <div style="white-space: pre-wrap;"><?= $a['konteudu'] ?></div>
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

<!-- Add Avizu Modal -->
<div class="modal fade" id="addAvizuModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="<?= base_url('administrador/avizu') ?>" method="post">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Aumenta Avizu Foun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Títulu Avizu</label>
                        <input type="text" name="titulu" class="form-control" required placeholder="Ez: Avizu Feriadu, Reuniaun...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konteúdu Avizu</label>
                        <textarea name="konteudu" class="form-control" rows="10" required placeholder="Hakere konteúdu avizu iha ne'e..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kansela</button>
                    <button type="submit" class="btn btn-primary">Publika Avizu</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>
