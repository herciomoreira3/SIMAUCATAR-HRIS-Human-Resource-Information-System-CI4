<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3"><strong>Jestaun</strong> Saláriu</h1>

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Taka/Loke Periodu Saláriu</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-5">
                        <form action="<?= base_url('administrador/salariu/periodu/lock') ?>" method="post" class="row g-2">
                            <?= csrf_field() ?>
                            <div class="col-4">
                                <input type="number" name="fulan" class="form-control" min="1" max="12" value="<?= date('n') ?>" required>
                            </div>
                            <div class="col-4">
                                <input type="number" name="tinan" class="form-control" min="2000" value="<?= date('Y') ?>" required>
                            </div>
                            <div class="col-4">
                                <button type="submit" class="btn btn-danger w-100">Taka</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-5">
                        <form action="<?= base_url('administrador/salariu/periodu/unlock') ?>" method="post" class="row g-2">
                            <?= csrf_field() ?>
                            <div class="col-4">
                                <input type="number" name="fulan" class="form-control" min="1" max="12" value="<?= date('n') ?>" required>
                            </div>
                            <div class="col-4">
                                <input type="number" name="tinan" class="form-control" min="2000" value="<?= date('Y') ?>" required>
                            </div>
                            <div class="col-4">
                                <button type="submit" class="btn btn-secondary w-100">Loke</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-2">
                        <div class="small text-muted">Periodu ne'ebe taka ona la bele prosesa saláriu foun.</div>
                    </div>
                </div>
                <?php if (!empty($payroll_periods)): ?>
                    <div class="mt-3">
                        <?php foreach (array_slice($payroll_periods, 0, 6) as $period): ?>
                            <span class="badge bg-<?= $period['status'] === 'Locked' ? 'danger' : 'secondary' ?> me-1">
                                <?= sprintf('%02d', $period['fulan']) ?>/<?= $period['tinan'] ?> <?= ($period['status'] === 'Locked') ? 'Taka' : 'Loke' ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Lista Saláriu Funsionáriu</h5>
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addPagamentuModal">Aumenta Pagamentu</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover my-0 datatable">
                        <thead>
                            <tr>
                                <th>NID</th>
                                <th>Funsionáriu</th>
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
                                    <td><?= esc($s['nid'] ?? '') ?></td>
                                    <td><?= esc($s['naran_kompletu'] ?? '') ?></td>
                                <td><?= sprintf("%02d", $s['fulan']) ?>/<?= $s['tinan'] ?></td>
                                <td>$ <?= number_format($s['salariu_baziku'], 2) ?></td>
                                <td>$ <?= number_format($s['total_subsidiu'], 2) ?></td>
                                <td>$ <?= number_format($s['total_deskontu'], 2) ?></td>
                                <td><strong>$ <?= number_format($s['salariu_liquidu'], 2) ?></strong></td>
                                <td><?= !empty($s['data_pagamentu']) ? date('d-m-Y', strtotime($s['data_pagamentu'])) : '-' ?></td>
                                <td>
                                    <span class="badge bg-<?= $s['estadu_pagamentu'] == 'Selu Ona' ? 'success' : 'warning' ?>">
                                        <?= esc($s['estadu_pagamentu']) ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalDetalluSalariu<?= $s['id'] ?>">Detallu</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php foreach($salariu as $s): ?>
                <?php $detallu = $salariu_detallu[$s['id']] ?? []; ?>
                <div class="modal fade" id="modalDetalluSalariu<?= $s['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Detallu Pagamentu - <?= esc($s['naran_kompletu'] ?? '-') ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <div>
                                        <div class="text-muted small">Periodu</div>
                                        <strong><?= sprintf("%02d", $s['fulan']) ?>/<?= $s['tinan'] ?></strong>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-muted small">Salariu Likuidu</div>
                                        <strong class="text-success">$ <?= number_format($s['salariu_liquidu'], 2) ?></strong>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm">
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
                                                <td><span class="badge bg-<?= $d['tipu'] === 'Deskontu' ? 'danger' : 'success' ?>"><?= esc($d['tipu']) ?></span></td>
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
                                            <tr><th colspan="2">Total Subsidiu</th><th class="text-end">$ <?= number_format($s['total_subsidiu'], 2) ?></th></tr>
                                            <tr><th colspan="2">Total Deskontu</th><th class="text-end text-danger">$ <?= number_format($s['total_deskontu'], 2) ?></th></tr>
                                            <tr><th colspan="2">Salariu Likuidu</th><th class="text-end text-success">$ <?= number_format($s['salariu_liquidu'], 2) ?></th></tr>
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
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Jestaun Subsídiu</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSubsidiuModal">Aumenta Subsídiu</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover my-0">
                            <thead>
                                <tr>
                                    <th>Naran Subsídiu</th>
                                    <th>Pozisaun Alvu</th>
                                    <th>Tipu Valór</th>
                                    <th>Valór Padrão</th>
                                    <th>Deskrisaun</th>
                                    <th>Asaun</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($subsidiu as $sub): ?>
                                <tr>
                                    <td><?= esc($sub['naran_subsidiu']) ?></td>
                                    <td><?= esc($sub['naran_pozisaun'] ?? 'Hotu-hotu') ?></td>
                                    <td><?= esc($sub['tipu_valor'] ?? 'Fiksu') ?></td>
                                    <td><?= (isset($sub['tipu_valor']) && $sub['tipu_valor'] == 'Persentajen') ? number_format($sub['valor_padrao'], 2).'%' : '$ '.number_format($sub['valor_padrao'], 2) ?></td>
                                    <td><?= esc($sub['deskrisaun']) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editSubsidiuModal<?= $sub['id'] ?>">Edit</button>
                                        <form action="<?= base_url('administrador/subsidiu/delete/'.$sub['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hamos subsidiu ne\'e?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-danger btn-sm">Hamos</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editSubsidiuModal<?= $sub['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="<?= base_url('administrador/subsidiu/update/'.$sub['id']) ?>" method="post">
                    <?= csrf_field() ?>
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Atualiza Subsídiu</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Naran Subsídiu</label>
                                                        <input type="text" name="naran_subsidiu" class="form-control" value="<?= esc($sub['naran_subsidiu']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Pozisaun Alvu (Opcionál)</label>
                                                        <select name="pozisaun_id" class="form-select">
                                                            <option value="">-- Aplika ba Hotu-hotu --</option>
                                                            <?php foreach($pozisaun as $p): ?>
                                                                <option value="<?= $p['id'] ?>" <?= ($sub['pozisaun_id'] == $p['id']) ? 'selected' : '' ?>><?= esc($p['naran_pozisaun']) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Tipu Valór</label>
                                                        <select name="tipu_valor" class="form-select" required>
                                                            <option value="Fiksu" <?= (($sub['tipu_valor'] ?? 'Fiksu') == 'Fiksu') ? 'selected' : '' ?>>Fiksu ($)</option>
                                                            <option value="Persentajen" <?= (($sub['tipu_valor'] ?? 'Fiksu') == 'Persentajen') ? 'selected' : '' ?>>Persentajen (%)</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Valór Padrão</label>
                                                        <input type="number" step="0.01" name="valor_padrao" class="form-control" value="<?= $sub['valor_padrao'] ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Deskrisaun</label>
                                                        <textarea name="deskrisaun" class="form-control" rows="2"><?= esc($sub['deskrisaun']) ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kansela</button>
                                                    <button type="submit" class="btn btn-primary">Atualiza</button>
                                                </div>
                                            </div>
                                        </form>
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

    <!-- Add Modal -->
    <div class="modal fade" id="addSubsidiuModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= base_url('administrador/subsidiu') ?>" method="post">
                    <?= csrf_field() ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Aumenta Subsídiu Foun</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Naran Subsídiu</label>
                            <input type="text" name="naran_subsidiu" class="form-control" required placeholder="Ez: Transporte, Alimentasaun...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pozisaun Alvu (Opcionál)</label>
                            <select name="pozisaun_id" class="form-select">
                                <option value="">-- Aplika ba Hotu-hotu --</option>
                                <?php foreach($pozisaun as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= esc($p['naran_pozisaun']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipu Valór</label>
                            <select name="tipu_valor" class="form-select" required>
                                <option value="Fiksu">Fiksu ($)</option>
                                <option value="Persentajen">Persentajen (%)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Valór Padrão</label>
                            <input type="number" step="0.01" name="valor_padrao" class="form-control" value="0.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskrisaun</label>
                            <textarea name="deskrisaun" class="form-control" rows="2" placeholder="Informasaun kona-ba subsidiu ne'e..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kansela</button>
                        <button type="submit" class="btn btn-primary">Rai Dadus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Selection Modal -->
    <div class="modal fade" id="selectionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Seleksiona Funsionáriu ba Pagamentu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Fulan</label>
                            <select id="filterFulan" class="form-select" onchange="loadFunsionariuStatus()">
                                <?php 
                                $months = ['Janeiru', 'Fevereiru', 'Marsu', 'Abril', 'Maiu', 'Juñu', 'Jullu', 'Agostu', 'Setembru', 'Outubru', 'Novembru', 'Dezembru'];
                                foreach($months as $idx => $m): ?>
                                    <option value="<?= $idx+1 ?>" <?= date('m') == $idx+1 ? 'selected' : '' ?>><?= $m ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tinan</label>
                            <input type="number" id="filterTinan" class="form-control" value="<?= date('Y') ?>" oninput="loadFunsionariuStatus()">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estadu Pagamentu</label>
                            <select id="filterStatus" class="form-select" onchange="loadFunsionariuStatus()">
                                <option value="All">Hotu-hotu</option>
                                <option value="Unpaid" selected>Seidauk Selu</option>
                                <option value="Paid">Selu Ona</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>NID</th>
                                    <th>Naran</th>
                                    <th>Pozisaun</th>
                                    <th>Estadu</th>
                                    <th>Asaun</th>
                                </tr>
                            </thead>
                            <tbody id="funsionariuStatusBody">
                                <!-- Data populated via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagamentu Detail Modal -->
    <div class="modal fade" id="addPagamentuModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="<?= base_url('administrador/salariu/prosesa') ?>" method="post">
                    <?= csrf_field() ?>
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white">Pagamentu Saláriu: <span id="paymentFunsionariuName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="funsionariu_id" id="hiddenFunsionariuId">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark">Periodu Pagamentu</label>
                                <div class="input-group">
                                    <input type="text" id="displayPeriod" class="form-control" readonly>
                                    <input type="hidden" name="fulan" id="hiddenFulan">
                                    <input type="hidden" name="tinan" id="hiddenTinan">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark">Saláriu Báziku ($)</label>
                                <input type="text" id="inputSalariuBaziku" class="form-control" readonly value="0.00">
                                <input type="hidden" name="salariu_baziku" id="hiddenSalariuBaziku" value="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark">Total Deskontu Jerál ($)</label>
                                <input type="number" step="0.01" name="total_deskontu" id="inputDeskontu" class="form-control" value="0.00" oninput="calculateTotal()">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-danger">Korta husi Sansaun ($)</label>
                                <input type="text" id="displaySansaunDedusaun" class="form-control" readonly value="0.00">
                                <input type="hidden" id="hiddenTotalSansaunOutstanding" value="0">
                                <input type="hidden" name="sansaun_dedusaun" id="hiddenSansaunDedusaun" value="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label text-dark">Hili Subsídiu</label>
                                <div class="border p-2 rounded" style="max-height: 150px; overflow-y: auto;">
                                    <?php foreach($subsidiu as $sub): ?>
                                        <div class="form-check">
                                            <input class="form-check-input subsidiu-check" type="checkbox" name="subsidiu_ids[]" value="<?= $sub['id'] ?>" data-naran="<?= esc($sub['naran_subsidiu']) ?>" data-valor="<?= $sub['valor_padrao'] ?>" data-tipu="<?= esc($sub['tipu_valor'] ?? 'Fiksu') ?>" data-pozisaun-id="<?= esc($sub['pozisaun_id'] ?? '') ?>" id="sub<?= $sub['id'] ?>" onchange="calculateTotal()">
                                            <label class="form-check-label text-dark" for="sub<?= $sub['id'] ?>">
                                                <?= esc($sub['naran_subsidiu']) ?> (<?= ($sub['tipu_valor'] ?? 'Fiksu') == 'Persentajen' ? number_format($sub['valor_padrao'], 2).'%' : '$'.number_format($sub['valor_padrao'], 2) ?>)
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="mt-2 text-dark">
                                    <strong>Total Subsídiu: $ <span id="totalSubsidiuText">0.00</span></strong>
                                    <input type="hidden" name="total_subsidiu" id="inputTotalSubsidiu" value="0">
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="text-end text-dark">
                            <h3 class="mb-0">Total Pagamentu (Líkuidu): $ <span id="totalLiquiduText">0.00</span></h3>
                            <input type="hidden" name="salariu_liquidu" id="inputSalariuLiquidu" value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="$('#addPagamentuModal').modal('hide'); $('#selectionModal').modal('show');">Fila</button>
                        <button type="submit" class="btn btn-success">Prosesa Pagamentu</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char];
        });
    }

    function encodePaymentPayload(value) {
        return btoa(unescape(encodeURIComponent(JSON.stringify(value))));
    }

    function decodePaymentPayload(value) {
        return JSON.parse(decodeURIComponent(escape(atob(value))));
    }

    // Update Aumenta Pagamentu button behavior
    document.querySelector('[data-bs-target="#addPagamentuModal"]').setAttribute('data-bs-target', '#selectionModal');
    
    // Auto-load on modal open
    document.getElementById('selectionModal').addEventListener('shown.bs.modal', function () {
        loadFunsionariuStatus();
    });

    function loadFunsionariuStatus() {
        var fulan = document.getElementById('filterFulan').value;
        var tinan = document.getElementById('filterTinan').value;
        var status = document.getElementById('filterStatus').value;

        fetch('<?= base_url('administrador/salariu/status') ?>?fulan=' + fulan + '&tinan=' + tinan)
            .then(response => response.json())
            .then(data => {
                var html = '';
                data.forEach(function(f) {
                    var isPaid = f.salariu_id != null;
                    
                    if (status == 'Paid' && !isPaid) return;
                    if (status == 'Unpaid' && isPaid) return;

                    var encodedFunsionariu = encodePaymentPayload(f);
                    html += '<tr>' +
                        '<td>' + escapeHtml(f.nid) + '</td>' +
                        '<td>' + escapeHtml(f.naran_kompletu) + '</td>' +
                        '<td>' + escapeHtml(f.naran_pozisaun) + '</td>' +
                        '<td>' + (isPaid ? '<span class="badge bg-success">Selu Ona</span>' : '<span class="badge bg-warning">Seidauk Selu</span>') + '</td>' +
                        '<td>' +
                        (isPaid ? '<button type="button" class="btn btn-secondary btn-sm" disabled>Selu</button>' :
                        '<button type="button" class="btn btn-success btn-sm" onclick="openPaymentDetail(\'' + encodedFunsionariu + '\')">Selu</button>') +
                        '</td>' +
                        '</tr>';
                });
                document.getElementById('funsionariuStatusBody').innerHTML = html;
            });
    }

    function openPaymentDetail(encodedData) {
        var f = decodePaymentPayload(encodedData);
        var fulan = document.getElementById('filterFulan').value;
        var tinan = document.getElementById('filterTinan').value;
        var monthName = document.getElementById('filterFulan').options[document.getElementById('filterFulan').selectedIndex].text;

        // Fill Modal
        document.getElementById('paymentFunsionariuName').innerText = f.naran_kompletu;
        document.getElementById('hiddenFunsionariuId').value = f.id;
        document.getElementById('displayPeriod').value = monthName + ' ' + tinan;
        document.getElementById('hiddenFulan').value = fulan;
        document.getElementById('hiddenTinan').value = tinan;
        document.getElementById('inputSalariuBaziku').value = parseFloat(f.salariu_baziku).toFixed(2);
        document.getElementById('hiddenSalariuBaziku').value = f.salariu_baziku;
        document.getElementById('hiddenTotalSansaunOutstanding').value = f.sansaun_dedusaun;
        document.getElementById('displaySansaunDedusaun').value = "0.00";
        document.getElementById('hiddenSansaunDedusaun').value = "0";

        // Reset fields
        document.getElementById('inputDeskontu').value = "0.00";
        document.querySelectorAll('.subsidiu-check').forEach(cb => {
            cb.checked = false;
            var subPozId = cb.getAttribute('data-pozisaun-id');
            var parentDiv = cb.closest('.form-check');
            if (subPozId && subPozId !== "" && subPozId != f.pozisaun_id) {
                parentDiv.style.display = 'none';
            } else {
                parentDiv.style.display = 'block';
            }
        });
        calculateTotal();

        // Switch Modals
        $('#selectionModal').modal('hide');
        $('#addPagamentuModal').modal('show');
    }

    function calculateTotal() {
        var baziku = parseFloat(document.getElementById('hiddenSalariuBaziku').value) || 0;
        var deskontu = parseFloat(document.getElementById('inputDeskontu').value) || 0;
        var totalOutstandingSansaun = parseFloat(document.getElementById('hiddenTotalSansaunOutstanding').value) || 0;
        var totalSubsidiu = 0;

        var checkboxes = document.querySelectorAll('.subsidiu-check:checked');
        checkboxes.forEach(function(checkbox) {
            var subVal = parseFloat(checkbox.getAttribute('data-valor')) || 0;
            var subTipu = checkbox.getAttribute('data-tipu');
            if (subTipu === 'Persentajen') {
                totalSubsidiu += (baziku * subVal) / 100;
            } else {
                totalSubsidiu += subVal;
            }
        });

        // Available balance before sanction deduction
        var available = baziku + totalSubsidiu - deskontu;
        
        // Deduction for this month is the smaller of:
        // 1. Total remaining sanction amount
        // 2. Total available salary (we don't allow negative liquid salary)
        var actualDeduction = Math.min(totalOutstandingSansaun, Math.max(0, available));

        var liquidu = available - actualDeduction;

        document.getElementById('totalSubsidiuText').innerText = totalSubsidiu.toFixed(2);
        document.getElementById('inputTotalSubsidiu').value = totalSubsidiu;
        
        // Update display and the hidden value that will be sent to the server
        document.getElementById('displaySansaunDedusaun').value = actualDeduction.toFixed(2);
        document.getElementById('hiddenSansaunDedusaun').value = actualDeduction;

        document.getElementById('totalLiquiduText').innerText = liquidu.toFixed(2);
        document.getElementById('inputSalariuLiquidu').value = liquidu;
    }
</script>
<?= $this->endSection(); ?>
