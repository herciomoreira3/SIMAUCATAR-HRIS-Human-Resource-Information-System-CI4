<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0"><strong>Bemvindu fali,</strong> <?= session()->get('fullname') ?? session()->get('username') ?>!</h1>
        <p class="text-muted mb-0">Hare ita-nia resumu dezempeñu prezensa no avizu foun.</p>
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
                <h5 class="card-title">Avizu husi Administrasaun</h5>
                <i data-feather="message-circle" class="text-muted" style="width: 18px; height: 18px;"></i>
            </div>
            <div class="card-body">
                <?php if(empty($avizu)): ?>
                    <div class="text-center py-5">
                        <i data-feather="mail" class="text-muted mb-2" style="width: 48px; height: 48px; opacity: 0.2;"></i>
                        <p class="text-muted">La iha avizu foun ba agora.</p>
                    </div>
                <?php else: ?>
                    <?php foreach(array_slice($avizu, 0, 3) as $av): ?>
                        <div class="mb-4 last-child-mb-0">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="font-medium text-dark mb-0"><?= $av['titulu'] ?></h6>
                                <span class="badge bg-primary-light text-primary" style="font-size: 10px;"><?= date('d M Y', strtotime($av['data_publikasaun'])) ?></span>
                            </div>
                            <div class="text-muted" style="font-size: 0.9rem; line-height: 1.6;">
                                <?= $av['konteudu'] ?>
                            </div>
                            <hr class="my-4 border-light">
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        new ApexCharts(document.querySelector("#chart-personal-perf"), {
            series: <?= $chart_data ?>,
            chart: {
                type: 'donut',
                height: 320,
                fontFamily: 'Inter, sans-serif'
            },
            labels: ['Prezente', 'Falta', 'Lisensa'],
            colors: ['#10b981', '#ef4444', '#3b82f6'],
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
    });
</script>
<?= $this->endSection(); ?>
