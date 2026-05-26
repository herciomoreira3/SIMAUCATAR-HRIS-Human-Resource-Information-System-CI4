<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #000; padding: 5px; text-align: right; }
        .table th { background-color: #f2f2f2; text-align: center; }
        .footer { margin-top: 20px; text-align: right; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SIMAUCATAR - HRIS</h2>
        <h3><?= $title ?></h3>
        <p>Fulan: <?= $fulan ?> / Tinan: <?= $tinan ?></p>
        <p>Data Print: <?= $data_print ?></p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th style="text-align: center;">NID</th>
                <th style="text-align: left;">Naran Kompletu</th>
                <th>Saláriu Báziku</th>
                <th>Total Subsídiu</th>
                <th>Total Deskontu</th>
                <th>Saláriu Líquidu</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach($salariu as $s): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td style="text-align: center;"><?= $s['nid'] ?></td>
                <td style="text-align: left;"><?= $s['naran_kompletu'] ?></td>
                <td>$<?= number_format($s['salariu_baziku'], 2) ?></td>
                <td>$<?= number_format($s['total_subsidiu'], 2) ?></td>
                <td>$<?= number_format($s['total_deskontu'], 2) ?></td>
                <td>$<?= number_format($s['salariu_liquidu'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Imprimidu husi sistema iha: <?= $data_print ?>
    </div>
</body>
</html>
