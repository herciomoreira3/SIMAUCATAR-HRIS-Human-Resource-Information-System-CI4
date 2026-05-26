<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3"><strong>Módulu</strong> Relatóriu</h1>

<div class="row">
    <div class="col-md-4 col-xl-3">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Menu Relatóriu</h5>
            </div>
            <div class="list-group list-group-flush" role="tablist">
                <a class="list-group-item list-group-item-action" href="<?= base_url('administrador/relatoriu/funsionariu') ?>">
                    <i class="align-middle me-1" data-feather="users"></i> Relatóriu Funsionáriu
                </a>
                <a class="list-group-item list-group-item-action" href="<?= base_url('administrador/relatoriu/prezensa') ?>">
                    <i class="align-middle me-1" data-feather="calendar"></i> Relatóriu Prezensa
                </a>
                <a class="list-group-item list-group-item-action" href="<?= base_url('administrador/relatoriu/salariu') ?>">
                    <i class="align-middle me-1" data-feather="dollar-sign"></i> Relatóriu Saláriu
                </a>
                <a class="list-group-item list-group-item-action" href="<?= base_url('administrador/relatoriu/lisensa') ?>">
                    <i class="align-middle me-1" data-feather="file-text"></i> Relatóriu Lisensa
                </a>
                <a class="list-group-item list-group-item-action" href="<?= base_url('administrador/relatoriu/sansaun') ?>">
                    <i class="align-middle me-1" data-feather="alert-octagon"></i> Relatóriu Sansaun
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-8 col-xl-9">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Deskrisaun Modulu</h5>
            </div>
            <div class="card-body">
                <p>Uza menu iha sorin karuk atu hili relatóriu ne'ebé ita hakarak haree. Ita bele filtra dadus bazeia ba tempu, departamentu, ka estadu husi kada dadus.</p>
                <p>Relatóriu hotu bele esporta ba formatu <strong>PDF</strong> ba imprime no <strong>Excel</strong> ba prosesamentu dadus lanjut.</p>
                
                <div class="alert alert-info" role="alert">
                    <div class="alert-message">
                        <strong>Informasaun:</strong> Vizualizasaun gráfiku sei mosu iha kada relatóriu atu fasilita monitorizasaun tendénsia.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
