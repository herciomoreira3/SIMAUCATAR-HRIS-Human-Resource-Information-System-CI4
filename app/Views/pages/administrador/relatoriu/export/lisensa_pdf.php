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
        <p>Periodu: <?= date('d/m/Y', strtotime($data_hahu)) ?> - <?= date('d/m/Y', strtotime($data_remata)) ?></p>
        <p>Data Print: <?= $data_print ?></p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th style="text-align: center;">NID</th>
                <th>Naran Kompletu</th>
                <th>Tipu Lisensa</th>
                <th>Data Hahu</th>
                <th>Data Remata</th>
                <th>Estadu</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach($lisensa as $l): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td style="text-align: center;"><?= $l['nid'] ?></td>
                <td><?= $l['naran_kompletu'] ?></td>
                <td><?= $l['tipu_lisensa'] ?></td>
                <td><?= date('d/m/Y', strtotime($l['data_hahu'])) ?></td>
                <td><?= date('d/m/Y', strtotime($l['data_remata'])) ?></td>
                <td><?= $l['estadu_lisensa'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Imprimidu husi sistema iha: <?= $data_print ?>
    </div>
</body>
</html>
