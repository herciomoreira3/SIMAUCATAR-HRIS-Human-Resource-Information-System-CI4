<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #000; padding: 5px; text-align: left; }
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
                <th>Naran Kompletu</th>
                <th>Tipu Sansaun</th>
                <th>Data Sansaun</th>
                <th>Valor Total</th>
                <th>Estadu</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach($sansaun as $s): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td style="text-align: center;"><?= $s['nid'] ?></td>
                <td><?= $s['naran_kompletu'] ?></td>
                <td><?= $s['naran_tipu'] ?></td>
                <td><?= date('d/m/Y', strtotime($s['data_sansaun'])) ?></td>
                <td>$<?= number_format($s['valor_total'], 2) ?></td>
                <td><?= $s['estadu_sansaun'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Imprimidu husi sistema iha: <?= $data_print ?>
    </div>
</body>
</html>
