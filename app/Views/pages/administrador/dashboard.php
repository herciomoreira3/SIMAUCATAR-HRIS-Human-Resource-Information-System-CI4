<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 mb-0"><strong>Dashboard</strong> Administrador</h1>
        <p class="text-muted mb-0">Benvindu fali, hare resumu atividade ohin nian.</p>
    </div>
    <div class="col-md-6 text-end d-flex align-items-center justify-content-end">
        <a href="<?= base_url('administrador/relatoriu') ?>" class="btn btn-dark shadow-sm">
            <i class="align-middle me-2" data-feather="bar-chart-2"></i> Relatóriu Jeral
        </a>
    </div>
</div>

<div class="row">
    <div class="col-sm-6 col-xl-3">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0">
                        <h5 class="card-title text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Funsionáriu</h5>
                    </div>
                    <div class="col-auto">
                        <div class="stat">
                            <i class="align-middle" data-feather="users"></i>
                        </div>
                    </div>
                </div>
                <h1 class="mt-1 mb-3"><?= $total_funsionariu ?></h1>
                <div class="mb-0">
                    <span class="badge bg-primary-light text-primary">Ativu</span>
                    <span class="text-muted ms-1" style="font-size: 0.75rem;">Total dadus</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0">
                        <h5 class="card-title text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Prezensa Ohin</h5>
                    </div>
                    <div class="col-auto">
                        <div class="stat">
                            <i class="align-middle" data-feather="calendar"></i>
                        </div>
                    </div>
                </div>
                <h1 class="mt-1 mb-3"><?= $total_prezensa_ohin ?></h1>
                <div class="mb-0">
                    <span class="text-muted" style="font-size: 0.75rem;">Funsionáriu tama ohin</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0">
                        <h5 class="card-title text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Lisensa Pendente</h5>
                    </div>
                    <div class="col-auto">
                        <div class="stat">
                            <i class="align-middle" data-feather="file-text"></i>
                        </div>
                    </div>
                </div>
                <h1 class="mt-1 mb-3"><?= $pendente_lisensa ?></h1>
                <div class="mb-0">
                    <span class="badge bg-danger-light text-danger">Pendente</span>
                    <span class="text-muted ms-1" style="font-size: 0.75rem;">Hein aprovasaun</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0">
                        <h5 class="card-title text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Avizu</h5>
                    </div>
                    <div class="col-auto">
                        <div class="stat">
                            <i class="align-middle" data-feather="bell"></i>
                        </div>
                    </div>
                </div>
                <h1 class="mt-1 mb-3"><?= count($avizu_ikus) ?></h1>
                <div class="mb-0">
                    <span class="text-muted" style="font-size: 0.75rem;">Total publikasaun</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8 d-flex">
        <div class="card flex-fill w-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">Tendénsia Prezensa</h5>
                <span class="text-muted" style="font-size: 0.75rem;">Loron 15 Ikus</span>
            </div>
            <div class="card-body">
                <div id="chart-prezensa-trend" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4 d-flex">
        <div class="card flex-fill w-100">
            <div class="card-header">
                <h5 class="card-title">Kompozisaun Departamentu</h5>
            </div>
            <div class="card-body d-flex">
                <div class="align-self-center w-100">
                    <div id="chart-dept-comp"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8 d-flex">
        <div class="card flex-fill">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">Avizu Ikus</h5>
                <a href="<?= base_url('administrador/avizu') ?>" class="text-xs text-primary text-decoration-none">Hare hotu</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Títulu</th>
                            <th>Data</th>
                            <th>Konteudu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($avizu_ikus)): ?>
                        <tr><td colspan="3" class="text-center py-4 text-muted">La iha avizu foun.</td></tr>
                        <?php else: ?>
                            <?php foreach(array_slice($avizu_ikus, 0, 5) as $av): ?>
                            <tr>
                                <td class="font-medium"><?= $av['titulu'] ?></td>
                                <td><span class="text-xs"><?= date('d M Y', strtotime($av['data_publikasaun'])) ?></span></td>
                                <td class="text-muted"><?= substr(strip_tags($av['konteudu']), 0, 40) ?>...</td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4 d-flex">
        <div class="card flex-fill">
            <div class="card-header">
                <h5 class="card-title">Sansaun foun</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if(empty($sansaun_ikus)): ?>
                    <li class="list-group-item text-center py-4 text-muted">La iha sansaun foun.</li>
                    <?php else: ?>
                        <?php foreach(array_slice($sansaun_ikus, 0, 5) as $s): ?>
                        <li class="list-group-item d-flex align-items-start gap-3">
                            <div class="bg-danger-light p-2 rounded-circle">
                                <i data-feather="alert-circle" class="text-danger" style="width: 14px; height: 14px;"></i>
                            </div>
                            <div>
                                <p class="mb-0 font-medium text-dark" style="font-size: 0.85rem;"><?= $s['naran_kompletu'] ?></p>
                                <small class="text-muted d-block" style="font-size: 0.75rem;"><?= $s['tipu_sansaun'] ?> • <?= date('d M Y', strtotime($s['data_sansaun'])) ?></small>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('javascript'); ?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Theme Colors
        const primary = '#2563eb';
        const secondary = '#64748b';
        const success = '#10b981';
        const danger = '#ef4444';
        const warning = '#f59e0b';

        // Attendance Trend Chart
        new ApexCharts(document.querySelector("#chart-prezensa-trend"), {
            series: [{
                name: 'Prezente',
                data: <?= $chart_prezente ?>
            }, {
                name: 'Falta',
                data: <?= $chart_falta ?>
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            colors: [primary, danger],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.3,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: <?= $chart_labels ?>,
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: secondary }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            },
            tooltip: { x: { format: 'dd/MM' } },
        }).render();

        // Department Composition Chart
        var deptData = <?= $dept_comp ?>;
        new ApexCharts(document.querySelector("#chart-dept-comp"), {
            series: deptData.map(item => parseInt(item.total)),
            chart: {
                type: 'donut',
                height: 320,
                fontFamily: 'Inter, sans-serif'
            },
            labels: deptData.map(item => item.naran_departamentu),
            colors: ['#2563eb', '#7c3aed', '#db2777', '#ea580c', '#16a34a', '#ca8a04'],
            stroke: { width: 0 },
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            legend: { position: 'bottom' }
        }).render();
    });
</script>
<?= $this->endSection(); ?>
