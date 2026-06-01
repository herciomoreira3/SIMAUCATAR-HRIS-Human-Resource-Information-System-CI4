<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3"><strong>Resibu</strong> Saláriu</h1>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Ha'u-nia Istória Saláriu</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover my-0 datatable">
                        <thead>
                            <tr>
                                <th>Fulan/Tinan</th>
                                <th>Saláriu Báziku</th>
                                <th>Subsídiu</th>
                                <th>Deskontu</th>
                                <th>Saláriu Líquidu</th>
                                <th>Data Pagamentu</th>
                                <th>Estadu</th>
                                <th>Asaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($salariu as $s): ?>
                            <tr>
                                <td><?= sprintf("%02d", $s['fulan']) ?>/<?= $s['tinan'] ?></td>
                                <td>$ <?= number_format($s['salariu_baziku'], 2) ?></td>
                                <td>$ <?= number_format($s['total_subsidiu'], 2) ?></td>
                                <td>$ <?= number_format($s['total_deskontu'], 2) ?></td>
                                <td><strong>$ <?= number_format($s['salariu_liquidu'], 2) ?></strong></td>
                                <td><?= !empty($s['data_pagamentu']) ? date('d-m-Y', strtotime($s['data_pagamentu'])) : '-' ?></td>
                                <td>
                                    <span class="badge bg-<?= $s['estadu_pagamentu'] == 'Selu Ona' ? 'success' : 'warning' ?>">
                                        <?= $s['estadu_pagamentu'] ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalSalariu<?= $s['id'] ?>">Detallu</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php foreach($salariu as $s): ?>
                <?php $detallu = $salariu_detallu[$s['id']] ?? []; ?>
                <div class="modal fade" id="modalSalariu<?= $s['id'] ?>" tabindex="-1" aria-labelledby="modalSalariuLabel<?= $s['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalSalariuLabel<?= $s['id'] ?>">Detallu Salariu <?= sprintf("%02d", $s['fulan']) ?>/<?= $s['tinan'] ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 h-100">
                                            <div class="text-muted small">Salariu Baziku</div>
                                            <div class="fs-5 fw-bold">$ <?= number_format($s['salariu_baziku'], 2) ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 h-100">
                                            <div class="text-muted small">Salariu Likuidu</div>
                                            <div class="fs-5 fw-bold text-success">$ <?= number_format($s['salariu_liquidu'], 2) ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead>
                                            <tr>
                                                <th>Komponente</th>
                                                <th>Tipu</th>
                                                <th class="text-end">Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Salariu Baziku</td>
                                                <td><span class="badge bg-primary">Baziku</span></td>
                                                <td class="text-end">$ <?= number_format($s['salariu_baziku'], 2) ?></td>
                                            </tr>
                                            <?php foreach($detallu as $d): ?>
                                            <tr>
                                                <td><?= esc($d['naran_komponente']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $d['tipu'] === 'Deskontu' ? 'danger' : 'success' ?>">
                                                        <?= esc($d['tipu']) ?>
                                                    </span>
                                                </td>
                                                <td class="text-end">$ <?= number_format($d['valor'], 2) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($detallu)): ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">La iha komponente adicional.</td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2">Total Subsidiu</th>
                                                <th class="text-end">$ <?= number_format($s['total_subsidiu'], 2) ?></th>
                                            </tr>
                                            <tr>
                                                <th colspan="2">Total Deskontu</th>
                                                <th class="text-end text-danger">$ <?= number_format($s['total_deskontu'], 2) ?></th>
                                            </tr>
                                            <tr>
                                                <th colspan="2">Salariu Likuidu</th>
                                                <th class="text-end text-success">$ <?= number_format($s['salariu_liquidu'], 2) ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Taka</button>
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
