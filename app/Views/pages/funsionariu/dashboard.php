<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0"><strong>Bemvindu fali,</strong> <?= esc(session()->get('fullname') ?? session()->get('username')) ?>!</h1>
        <p class="text-muted mb-0">Hare ita-nia resumu dezempeñu prezensa no anunsiu foun.</p>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-4 d-flex">
        <div class="card flex-fill">
            <div class="card-header">
                <h5 class="card-title">Estatístika Prezensa</h5>
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                <div id="chart-personal-perf"></div>
                <div class="text-center mt-3">
                    <p class="text-muted text-xs mb-0">Resumu dezempeñu prezensa pessoál fulan ne'e nian.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-8 d-flex">
        <div class="card flex-fill">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">Anunsiu husi Administrasaun</h5>
                <i data-feather="message-circle" class="text-muted" style="width: 18px; height: 18px;"></i>
            </div>
            <div class="card-body">
                <?php if(empty($avizu)): ?>
                    <div class="text-center py-5">
                        <i data-feather="mail" class="text-muted mb-2" style="width: 48px; height: 48px; opacity: 0.2;"></i>
                        <p class="text-muted">La iha anunsiu foun ba agora.</p>
                    </div>
                <?php else: ?>
                    <?php foreach(array_slice($avizu, 0, 3) as $av): ?>
                        <div class="mb-4 last-child-mb-0">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="font-medium text-dark mb-0"><?= esc($av['titulu']) ?></h6>
                                <span class="badge bg-primary-light text-primary" style="font-size: 10px;"><?= date('d M Y', strtotime($av['data_publikasaun'])) ?></span>
                            </div>
                            <div class="text-muted" style="font-size: 0.9rem; line-height: 1.6;">
                                <?= esc($av['konteudu']) ?>
                            </div>
                            <hr class="my-4 border-light">
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 d-flex">
        <div class="card flex-fill">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">Tendénsia Prezensa Pessoál</h5>
                <span class="text-muted" style="font-size: 0.75rem;">Loron 15 Ikus</span>
            </div>
            <div class="card-body">
                <div id="chart-personal-trend" style="min-height: 330px;"></div>
            </div>
        </div>
    </div>
</div>

<style>
    .last-child-mb-0:last-child hr {
        display: none;
    }
    .last-child-mb-0:last-child {
        margin-bottom: 0 !important;
    }
</style>

<?= $this->endSection(); ?>

<?= $this->section('javascript'); ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof ApexCharts === 'undefined') {
            document.querySelectorAll('#chart-personal-perf, #chart-personal-trend').forEach(function (el) {
                el.innerHTML = '<div class="text-center text-muted py-5">Gráfiku la bele loke. Favor halo refresh pajina.</div>';
            });
            return;
        }

        const success = '#10b981';
        const warning = '#f59e0b';
        const danger = '#ef4444';
        const info = '#3b82f6';
        const secondary = '#64748b';

        new ApexCharts(document.querySelector("#chart-personal-perf"), {
            series: <?= $chart_data ?>,
            chart: {
                type: 'donut',
                height: 320,
                fontFamily: 'Inter, sans-serif'
            },
            labels: ['Prezente', 'Loron Sorin', 'Falta', 'Lisensa'],
            colors: [success, warning, danger, info],
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
            legend: {
                position: 'bottom'
            }
        }).render();

        new ApexCharts(document.querySelector("#chart-personal-trend"), {
            series: [{
                name: 'Prezente',
                data: <?= $trend_prezente ?>
            }, {
                name: 'Loron Sorin',
                data: <?= $trend_loron_sorin ?>
            }, {
                name: 'Falta',
                data: <?= $trend_falta ?>
            }, {
                name: 'Lisensa',
                data: <?= $trend_lisensa ?>
            }],
            chart: {
                type: 'area',
                height: 330,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            colors: [success, warning, danger, info],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.25,
                    opacityTo: 0.06,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: <?= $trend_labels ?>,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: secondary } }
            },
            yaxis: {
                min: 0,
                forceNiceScale: true,
                labels: { style: { colors: secondary } }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            }
        }).render();
    });
</script>
<?= $this->endSection(); ?>
