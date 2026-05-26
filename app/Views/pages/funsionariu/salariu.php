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
                                    <button class="btn btn-sm btn-info">Detallu</button>
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
