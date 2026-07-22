<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #000; padding: 5px; text-align: center; }
        .table th { background-color: #f2f2f2; }
        .footer { margin-top: 20px; text-align: right; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SIMAUCATAR - HRIS</h2>
        <h3><?= $title ?></h3>
        <p>Periodu: <?= date('d/m/Y', strtotime($data_hahu)) ?> - <?= date('d/m/Y', strtotime($data_remata)) ?></p>
        <p>Data Print: <?= $data_print ?></p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>NID</th>
                <th>Naran Kompletu</th>
                <th>Diresaun</th>
                <th>Prezente</th>
                <th>Loron Sorin</th>
                <th>Falta</th>
                <th>Lisensa</th>
                <th>Incomplete</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach($prezensa as $p): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $p['nid'] ?></td>
                <td style="text-align: left;"><?= $p['naran_kompletu'] ?></td>
                <td><?= $p['naran_diresaun'] ?></td>
                <td><?= $p['total_prezente'] ?></td>
                <td><?= $p['total_loron_sorin'] ?></td>
                <td><?= $p['total_falta'] ?></td>
                <td><?= $p['total_lisensa'] ?></td>
                <td><?= $p['total_incomplete'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Imprimidu husi sistema iha: <?= $data_print ?>
    </div>
</body>
</html>
