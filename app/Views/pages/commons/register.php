<!DOCTYPE html>
<html lang="tet">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistema manejamentu funsionariu Postu Administrativu Maucatar">
    <meta name="author" content="Gilang Heavy">
    <meta name="keywords" content="SI Maucatar, sistema funsionariu, postu administrativu, Maucatar">

    <title>SI - Maucatar | Rejistu</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">

    <script src="<?= base_url('assets/js/app.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/app.js') ?>"></script>

</head>

<body>
    <main class="d-flex w-100">
        <div class="container d-flex flex-column">
            <div class="row vh-100">
                <div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">
                    <div class="d-table-cell align-middle">

                        <div class="card">
                            <form action="<?= base_url('register'); ?>" method="POST">
                    <?= csrf_field() ?>
                                <div class="card-body">
                                    <?= $this->include('components/alerts'); ?>

                                    <div class="text-center mt-4">
                                        <h1 class="h2">Bemvindu mai SI Maucatar</h1>
                                        <p class="lead"> Rejistu Akun Foun </p>
                                    </div>

                                    <div class="m-sm-4">
                                        <!-- 
                                            <div class="text-center">
                                                <img src="" alt="Charles Hall" class="img-fluid rounded-circle" width="132" height="132" />
                                            </div>
                                         -->
                                        <div class="mb-3">
                                            <label class="form-label">Naran Kompletu</label>
                                            <input class="form-control form-control-lg" type="text" name="inputFullname" placeholder="Hatama ita-nia naran" required />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input class="form-control form-control-lg" type="email" name="inputEmail" placeholder="Hatama ita-nia email" required />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Senha</label>
                                            <input class="form-control form-control-lg" type="password" name="inputPassword" placeholder="Hatama ita-nia senha" required />

                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Konfirma Senha</label>
                                            <input class="form-control form-control-lg" type="password" name="inputPassword2" placeholder="Hatama fali ita-nia senha" required />
                                            <small>
                                                <a href="<?= base_url() ?>">Iha ona akun? Tama</a>
                                            </small>
                                        </div>
                                    </div>

                                    <div class="text-end mt-3">
                                        <button type="submit" class="btn btn-lg btn-primary">Rejistu</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
</body>

</html>
