<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3"><strong>Jestaun</strong> Sansaun</h1>

<div class="row">
    <div class="col-12">
        <div class="tab">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item"><a class="nav-link active" href="#tab-1" data-bs-toggle="tab" role="tab">Lista Sansaun</a></li>
                <li class="nav-item"><a class="nav-link" href="#tab-2" data-bs-toggle="tab" role="tab">Jestaun Tipu Sansaun</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-1" role="tabpanel">
                    <div class="card shadow-none">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Sansaun Funsionáriu</h5>
                            <div>
                                <button class="btn btn-warning btn-sm me-2" data-bs-toggle="modal" data-bs-target="#geraSansaunAbsensiaModal">Jera Sansaun Absénsia</button>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#selectFunsionariuModal">Fo Sansaun</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover my-0 datatable">
                                    <thead>
                                        <tr>
                                            <th>Funsionáriu</th>
                                            <th>Tipu Sansaun</th>
                                            <th>Kategoria</th>
                                            <th>Estadu</th>
                                            <th>Data</th>
                                            <th>Asaun</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($sansaun as $s): ?>
                                        <tr>
                                            <td><strong><?= $s['naran_kompletu'] ?></strong><br><small><?= $s['nid'] ?></small></td>
                                            <td><?= $s['naran_tipu'] ?></td>
                                            <td>
                                                <span class="badge bg-<?= $s['kategoria'] == 'Korta Saláriu' ? 'danger' : ($s['kategoria'] == 'Hatun Pozisaun' ? 'warning' : 'secondary') ?>">
                                                    <?= $s['kategoria'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if($s['estadu_sansaun'] == 'Ativu'): ?>
                                                    <span class="badge bg-primary">Ativu</span>
                                                <?php elseif($s['estadu_sansaun'] == 'Retira'): ?>
                                                    <span class="badge bg-secondary">Retira</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Konkluidu</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d-m-Y', strtotime($s['data_sansaun'])) ?></td>
                                            <td>
                                                <button class="btn btn-info btn-sm btn-review-sansaun" data-id="<?= $s['id'] ?>">Review</button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab-2" role="tabpanel">
                    <div class="card shadow-none">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Tipu Sansaun</h5>
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addTipuSansaunModal">Aumenta Tipu</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover my-0">
                                    <thead>
                                        <tr>
                                            <th>Naran Tipu</th>
                                            <th>Kategoria</th>
                                            <th>Valór Korta ($)</th>
                                            <th>Asaun</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($tipu_sansaun as $ts): ?>
                                        <tr>
                                            <td><?= $ts['naran_tipu'] ?></td>
                                            <td><?= $ts['kategoria'] ?></td>
                                            <td><?= $ts['kategoria'] == 'Korta Saláriu' ? '$ '.number_format($ts['valor_dedusaun'], 2) : '-' ?></td>
                                            <td>
                                                <form action="<?= base_url('administrador/tipu_sansaun/delete/'.$ts['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hamos tipu sansaun ne\'e?')">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="btn btn-danger btn-sm">Hamos</button>
                                                </form>
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
        </div>
    </div>
</div>

<!-- Modal Hili Funsionariu (DataTable Pop-up) -->
<div class="modal fade" id="selectFunsionariuModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">Hili Funsionáriu atu fo Sansaun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover datatable" style="width:100%">
                        <thead>
                            <tr>
                                <th>NID</th>
                                <th>Naran Kompletu</th>
                                <th>Diresaun</th>
                                <th>Pozisaun</th>
                                <th>Asaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($funsionariu as $f): ?>
                            <tr>
                                <td><?= $f['nid'] ?></td>
                                <td><?= $f['naran_kompletu'] ?></td>
                                <td><?= $f['naran_diresaun'] ?></td>
                                <td><?= $f['naran_pozisaun'] ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm btn-fo-sansaun" 
                                        data-id="<?= $f['id'] ?>" 
                                        data-name="<?= $f['naran_kompletu'] ?>"
                                        data-nid="<?= $f['nid'] ?>">
                                        Fo Sansaun
                                    </button>
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

<!-- Modal Add Tipu Sansaun -->
<div class="modal fade" id="addTipuSansaunModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= base_url('administrador/tipu_sansaun') ?>" method="post">
                    <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white">Aumenta Tipu Sansaun Foun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Naran Tipu Sansaun</label>
                        <input type="text" name="naran_tipu" class="form-control" required placeholder="Ez: SP1, Korta Saláriu 5%, nst">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategoria</label>
                        <select name="kategoria" class="form-select" id="selectKategoria" onchange="toggleValorField()">
                            <option value="Jeral">Jeral (Anunsiu, nst)</option>
                            <option value="Korta Saláriu">Korta Saláriu (Salary Deduction)</option>
                            <option value="Hatun Pozisaun">Hatun Pozisaun (Demotion)</option>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="valorField">
                        <label class="form-label">Valór Korta ($)</label>
                        <input type="number" step="0.01" name="valor_dedusaun" class="form-control" value="0.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kansela</button>
                    <button type="submit" class="btn btn-success">Rai Tipu</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Fo Sansaun (Specific Form) -->
<div class="modal fade" id="addSansaunModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="<?= base_url('administrador/sansaun') ?>" method="post">
                    <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="sansaunModalTitle">Fo Sansaun ba Funsionáriu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="funsionariu_id" id="targetFunsionariuId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Funsionáriu</label>
                            <input type="text" class="form-control bg-light" id="targetFunsionariuName" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Data Sansaun</label>
                            <input type="date" name="data_sansaun" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipu Sansaun</label>
                        <select name="tipu_sansaun_id" class="form-select" id="selectSansaunType" required onchange="checkSansaunCategory()">
                            <option value="">- Seleksiona Tipu -</option>
                            <?php foreach($tipu_sansaun as $ts): ?>
                            <option value="<?= $ts['id'] ?>" data-kategoria="<?= $ts['kategoria'] ?>"><?= $ts['naran_tipu'] ?> (<?= $ts['kategoria'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3 d-none" id="demotionField">
                        <label class="form-label text-danger font-weight-bold">Pozisaun Foun (Hatun Pozisaun)</label>
                        <select name="new_pozisaun_id" class="form-select">
                            <option value="">- Hili Pozisaun Foun -</option>
                            <?php foreach($pozisaun as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= $p['naran_pozisaun'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Atenun: Pozisaun funsionáriu sei troka automatikamente wainhira submete.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Motivu / Deskrisaun</label>
                        <textarea name="motivu" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#selectFunsionariuModal">Fila ba Lista</button>
                    <button type="submit" class="btn btn-primary">Submete Sansaun</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Review Detail Sansaun -->
<div class="modal fade" id="reviewSansaunModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title text-white">Detallu Sansaun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm">
                    <tr>
                        <th width="40%">Funsionáriu</th>
                        <td id="revFunsionariu"></td>
                    </tr>
                    <tr>
                        <th>Tipu Sansaun</th>
                        <td id="revTipu"></td>
                    </tr>
                    <tr>
                        <th>Kategoria</th>
                        <td id="revKategoria"></td>
                    </tr>
                    <tr>
                        <th>Data</th>
                        <td id="revData"></td>
                    </tr>
                    <tr id="revValorRow">
                        <th>Total Korta</th>
                        <td id="revValor"></td>
                    </tr>
                    <tr id="revPagaduRow">
                        <th>Selu ona</th>
                        <td id="revPagadu" class="text-success font-weight-bold"></td>
                    </tr>
                    <tr>
                        <th>Estadu</th>
                        <td id="revEstadu"></td>
                    </tr>
                </table>
                <div class="mb-2">
                    <strong>Motivu:</strong>
                    <p id="revMotivu" class="p-2 bg-light border rounded"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Taka</button>
                <form action="#" method="post" id="formRetiraSansaun" class="d-inline d-none" onsubmit="return confirm('Retira sansaun ne\'e? Dedusaun sei para automatikamente.')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="retira_reason" value="Retira husi review sansaun">
                    <button type="submit" id="btnRetiraSansaun" class="btn btn-warning">Retira Sansaun</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Jera Sansaun Absensia -->
<div class="modal fade" id="geraSansaunAbsensiaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= base_url('administrador/sansaun/jera_absensia') ?>" method="post">
                    <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Jera Sansaun Absénsia Automátiku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
                </div>
                <div class="modal-body">
                    <p>Sistema sei kalkula automatikamente falta husi funsionáriu hotu iha fulan ne'ebé hili. Funsionáriu ne'ebé falta dala 3 ka liu sei hetan korta saláriu 0.9% ba kada falta dala 3.</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fulan</label>
                            <select name="fulan" class="form-select" required>
                                <?php 
                                $months = ['Janeiru', 'Fevereiru', 'Marsu', 'Abril', 'Maiu', 'Juñu', 'Jullu', 'Agostu', 'Setembru', 'Outubru', 'Novembru', 'Dezembru'];
                                foreach($months as $idx => $m): ?>
                                    <option value="<?= $idx+1 ?>" <?= date('m') == $idx+1 ? 'selected' : '' ?>><?= $m ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tinan</label>
                            <input type="number" name="tinan" class="form-control" value="<?= date('Y') ?>" required>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        Atenun: Sansaun ne'ebé jera ona ba fulan ne'e sei la jera dala-rua (evita duplikadu).
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kansela</button>
                    <button type="submit" class="btn btn-warning" onclick="return confirm('Jera sansaun absénsia agora?')">Jera Agora</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Review button
        document.querySelectorAll('.btn-review-sansaun').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                fetch('<?= base_url('administrador/sansaun/detail/') ?>' + id)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('revFunsionariu').innerText = data.naran_kompletu + ' (' + data.nid + ')';
                        document.getElementById('revTipu').innerText = data.naran_tipu;
                        document.getElementById('revKategoria').innerText = data.kategoria;
                        document.getElementById('revData').innerText = data.data_sansaun;
                        document.getElementById('revMotivu').innerText = data.motivu;
                        
                        const estaduBadge = data.estadu_sansaun === 'Ativu' ? '<span class="badge bg-primary">Ativu</span>' : 
                                            (data.estadu_sansaun === 'Retira' ? '<span class="badge bg-secondary">Retira</span>' : '<span class="badge bg-success">Konkluidu</span>');
                        document.getElementById('revEstadu').innerHTML = estaduBadge;

                        if (data.kategoria === 'Korta Saláriu') {
                            document.getElementById('revValorRow').classList.remove('d-none');
                            document.getElementById('revPagaduRow').classList.remove('d-none');
                            document.getElementById('revValor').innerText = '$ ' + parseFloat(data.valor_total).toFixed(2);
                            document.getElementById('revPagadu').innerText = '$ ' + parseFloat(data.valor_pagadu).toFixed(2);
                        } else {
                            document.getElementById('revValorRow').classList.add('d-none');
                            document.getElementById('revPagaduRow').classList.add('d-none');
                        }

                        const formRetira = document.getElementById('formRetiraSansaun');
                        if (data.estadu_sansaun === 'Ativu') {
                            formRetira.classList.remove('d-none');
                            formRetira.setAttribute('action', '<?= base_url('administrador/sansaun/retira/') ?>' + data.id);
                        } else {
                            formRetira.classList.add('d-none');
                        }

                        new bootstrap.Modal(document.getElementById('reviewSansaunModal')).show();
                    });
            });
        });

        // Handle Fo Sansaun button in the selection modal using event delegation
        document.body.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('btn-fo-sansaun')) {
                const button = e.target;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const nid = button.getAttribute('data-nid');
                
                // Set data to the form modal
                document.getElementById('targetFunsionariuId').value = id;
                document.getElementById('targetFunsionariuName').value = nid + ' - ' + name;
                document.getElementById('sansaunModalTitle').innerText = 'Fo sansaun ba ' + name;
                
                // Hide current modal
                const selectModalEl = document.getElementById('selectFunsionariuModal');
                const selectModal = bootstrap.Modal.getInstance(selectModalEl);
                if (selectModal) {
                    selectModal.hide();
                } else {
                    $('#selectFunsionariuModal').modal('hide');
                }
                
                // Show form modal with a slight delay to avoid backdrop conflicts
                setTimeout(() => {
                    let formModal = bootstrap.Modal.getInstance(document.getElementById('addSansaunModal'));
                    if (!formModal) {
                        formModal = new bootstrap.Modal(document.getElementById('addSansaunModal'));
                    }
                    formModal.show();
                }, 400);
            }
        });
    });

    // Fix DataTables rendering inside Bootstrap modal
    $('#selectFunsionariuModal').on('shown.bs.modal', function () {
        if ($.fn.DataTable.isDataTable('#selectFunsionariuModal .datatable')) {
            $('#selectFunsionariuModal .datatable').DataTable().columns.adjust();
        }
    });

    function toggleValorField() {
        const kat = document.getElementById('selectKategoria').value;
        const field = document.getElementById('valorField');
        if (kat === 'Korta Saláriu') {
            field.classList.remove('d-none');
        } else {
            field.classList.add('d-none');
        }
    }

    function checkSansaunCategory() {
        const select = document.getElementById('selectSansaunType');
        const selectedOption = select.options[select.selectedIndex];
        const kategoria = selectedOption.getAttribute('data-kategoria');
        const demotionField = document.getElementById('demotionField');
        
        if (kategoria === 'Hatun Pozisaun') {
            demotionField.classList.remove('d-none');
            demotionField.querySelector('select').setAttribute('required', 'required');
        } else {
            demotionField.classList.add('d-none');
            demotionField.querySelector('select').removeAttribute('required');
        }
    }
</script>

<?= $this->endSection(); ?>
