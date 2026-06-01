<!DOCTYPE html>
<html lang="tet">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Sistema manejamentu funsionariu Postu Administrativu Maucatar">
	<meta name="author" content="Gilang Heavy">
	<meta name="keywords" content="SI Maucatar, sistema funsionariu, postu administrativu, Maucatar">

	<title>SI - Maucatar <?= isset($title) ? ' | ' . $title : '' ?></title>

	<link rel="icon" type="image/png" href="https://timor-leste.gov.tl/wp-content/themes/timor/images/logo.png" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
	<link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
	<link href="<?= base_url('assets/css/style-custom.css') ?>" rel="stylesheet">
	<meta name="csrf-token-name" content="<?= csrf_token() ?>">
	<meta name="csrf-token-value" content="<?= csrf_hash() ?>">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script>
		if (window.jQuery) {
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token-value"]').getAttribute('content')
				}
			});
		}
	</script>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body data-theme="light">
	<div class="wrapper">
		<?= $this->include('layouts/sidebar'); ?>
		<div class="main">
			<?= $this->include('layouts/header'); ?>
			<main class="content">
				<div class="container-fluid p-0">
					<?= $this->include('components/alerts'); ?>
					<?= $this->renderSection('content'); ?>
				</div>
			</main>
			<?= $this->include('layouts/footer'); ?>
		</div>
	</div>
	<script src="<?= base_url('assets/js/app.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/app.js') ?>"></script>
	<?= $this->renderSection('javascript'); ?>
</body>

</html>
