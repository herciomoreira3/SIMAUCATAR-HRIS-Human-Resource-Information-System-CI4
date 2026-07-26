<?php
// Legacy pages still initialise some plugins inline, so their dependencies must be
// available before the page body is parsed. Keep this list deliberately route-based
// until those initialisers are migrated to page entries.
$assetPath = trim(service('uri')->getPath(), '/');
$dataTablePaths = [
	'administrador/avizu', 'administrador/documentu',
	'administrador/feriadu', 'administrador/funsionariu', 'administrador/grau',
	'administrador/kategoria', 'administrador/leave_balance', 'administrador/lisensa',
	'administrador/pozisaun', 'administrador/prezensa', 'administrador/salariu',
	'administrador/sansaun', 'funsionariu/dokumentu', 'funsionariu/lisensa',
	'funsionariu/prezensa', 'funsionariu/salariu',
];
$needsDataTables = in_array($assetPath, $dataTablePaths, true)
	|| str_starts_with($assetPath, 'administrador/relatoriu/');
$needsJquery = $needsDataTables
	|| in_array($assetPath, ['users', 'users/role-access'], true);
$needsDialog = session()->getFlashdata('success') !== null || session()->getFlashdata('error') !== null;

// Build outputs are content-addressed and may be cached immutable. Regenerate them
// with scripts/build-assets.ps1 whenever a source asset changes.
$coreJsAsset = 'assets/build/core-js.3b65f1d6d37091b5.js';
$coreCssAsset = 'assets/build/core-css.5b9d05c01dc7e8e7.css';
$customCssAsset = 'assets/build/custom-css.0633ff00c7efd352.css';
?>
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

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="icon" type="image/png" href="<?= base_url('favicon.ico') ?>" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
	<link href="<?= base_url($coreCssAsset) ?>" rel="stylesheet">
	<link href="<?= base_url($customCssAsset) ?>" rel="stylesheet">
	<meta name="csrf-token-name" content="<?= csrf_token() ?>">
	<meta name="csrf-token-value" content="<?= csrf_hash() ?>">

	<?php if ($needsJquery): ?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<?php endif; ?>
	<script>
		if (window.jQuery) {
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token-value"]').getAttribute('content')
				}
			});
		}
	</script>
	<?php if ($needsDataTables): ?>
	<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
	<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
	<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
	<?php endif; ?>
	<?php if ($needsDialog): ?>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<?php endif; ?>

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
	<script src="<?= base_url($coreJsAsset) ?>"></script>
	<?= $this->renderSection('javascript'); ?>
</body>

</html>
