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
        .table th { background-color: #f2f2f2; }
        .footer { margin-top: 20px; text-align: right; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SIMAUCATAR - HRIS</h2>
        <h3><?= $title ?></h3>
        <p>Data Print: <?= $data_print ?></p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>NID</th>
                <th>Naran Kompletu</th>
                <th>Departamentu</th>
                <th>Pozisaun</th>
                <th>Kategoria</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach($funsionariu as $f): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $f['nid'] ?></td>
                <td><?= $f['naran_kompletu'] ?></td>
                <td><?= $f['naran_departamentu'] ?></td>
                <td><?= $f['naran_pozisaun'] ?></td>
                <td><?= $f['naran_kategoria'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Imprimidu husi sistema iha: <?= $data_print ?>
    </div>
</body>
</html>
