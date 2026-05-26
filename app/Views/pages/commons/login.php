<!DOCTYPE html>
<html lang="tet">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SI - Maucatar | Login</title>

    <link rel="icon" type="image/png" href="https://timor-leste.gov.tl/wp-content/themes/timor/images/logo.png" />

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/style-custom.css') ?>" rel="stylesheet">

    <style>
        body {
            background-color: #f9fafb !important;
            font-family: 'Inter', sans-serif;
        }
        .login-card {
            border: none !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            background: #ffffff !important;
        }
        .logo-container {
            width: 100px;
            height: 100px;
            margin: 0 auto 1.5rem;
        }
        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    </style>
</head>

<body>
    <main class="d-flex w-100 vh-100 align-items-center">
        <div class="container d-flex flex-column">
            <div class="row">
                <div class="col-sm-10 col-md-8 col-lg-5 mx-auto">
                    
                    <?= $this->include('components/alerts'); ?>

                    <div class="card login-card p-2 p-md-4">
                        <div class="card-body">
                            <!-- Header Inside Card -->
                            <div class="text-center mb-5">
                                <div class="mb-4">
                                    <img src="https://timor-leste.gov.tl/wp-content/themes/timor/images/logo.png" alt="Logo Timor-Leste" class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #f1f5f9;">
                                </div>
                                <h1 class="h4 font-bold text-dark mb-2" style="font-weight: 700; line-height: 1.4; font-size: 1.15rem;">
                                    Sistema Manajementu Dadus Funsionariu<br>Postu Administrativu Maucatar
                                </h1>
                                <p class="text-muted font-medium mb-0" style="font-size: 0.9rem;">Munisipiu Covalima</p>
                            </div>

                            <form action="<?= base_url('login'); ?>" method="POST">
                                <div class="mb-4">
                                    <label class="form-label">Email</label>
                                    <input class="form-control" type="email" name="inputEmail" placeholder="Hatama ita-nia email" required />
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Password</label>
                                    <input class="form-control" type="password" name="inputPassword" placeholder="Hatama ita-nia password" required />
                                </div>
                                <div class="mb-3 d-flex align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="rememberMe" name="rememberMe">
                                        <label class="form-check-label text-muted" for="rememberMe" style="font-size: 13px;">
                                            Memória ha'u
                                        </label>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary w-100 py-2" style="font-size: 15px; font-weight: 600;">
                                        Tama Agora
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <p class="text-center text-muted mt-5" style="font-size: 11px; opacity: 0.7;">
                        &copy; <?= date('Y') ?> - SI Maucatar. All Rights Reserved.
                    </p>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>

</html>