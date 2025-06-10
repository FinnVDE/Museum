<?php
// Load QR codes from JSON file
$jsonFile = __DIR__ . '/includes/qr-codes.json';
$qrCodes = [];
if (file_exists($jsonFile)) {
    $jsonData = file_get_contents($jsonFile);
    $qrCodes = json_decode($jsonData, true) ?? [];
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_index'])) {
        $deleteIndex = (int)$_POST['delete_index'];
        if (isset($qrCodes[$deleteIndex])) {
            array_splice($qrCodes, $deleteIndex, 1);
            file_put_contents($jsonFile, json_encode($qrCodes, JSON_PRETTY_PRINT));
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    }
    // Handle duplicate action
    if (isset($_POST['duplicate_index'])) {
        $duplicateIndex = (int)$_POST['duplicate_index'];
        if (isset($qrCodes[$duplicateIndex])) {
            $qrCodes[] = $qrCodes[$duplicateIndex];
            file_put_contents($jsonFile, json_encode($qrCodes, JSON_PRETTY_PRINT));
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Museumstukken Overzicht</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #007bff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .dashboard-container {
            width: fit-content;
            max-width: 1250px;
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            margin: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .table-container {
            margin-top: 20px;
        }

        table.table {
            margin: 0 auto;
        }

        img.qr-image {
            max-width: 80px;
            height: auto;
        }

        .btn-group .btn {
            width: 40px;
            height: 40px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        h2 {
            font-weight: bold;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <div class="dashboard-inner-container">
            <h2 class="text-center">📜QR Codes Overzicht</h2>
            <div class="table-container">
                <table class="table table-striped text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Titel</th>
                            <th>Tekst</th>
                            <th>Tijdsperk</th>
                            <th>QR Code</th>
                            <th>Afbeelding</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($qrCodes) === 0): ?>
                            <tr>
                                <td colspan="6">Geen QR codes gevonden.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($qrCodes as $index => $qr): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($qr['title']) ?>
                                    </td>
                                    <td><?= $qr['content'] ?></td>
                                    <td><?= htmlspecialchars($qr['era']) ?></td>
                                    <td>
                                        <?php if (!empty($qr['qr_code'])): ?>
                                            <?php
                                            $qrCode = $qr['qr_code'];
                                            $isImageUrl = preg_match('/\.(png|jpg|jpeg|svg)$/i', $qrCode);
                                            ?>
                                            <?php if ($isImageUrl): ?>
                                                <img src="<?= htmlspecialchars($qrCode) ?>" alt="QR Code" class="qr-image" />
                                            <?php else: ?>
                                                <img src="https://chart.googleapis.com/chart?cht=qr&chs=80x80&chl=<?= urlencode($qrCode) ?>" alt="QR Code" class="qr-image" />
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled title="Geen QR code link"><i class="fas fa-qrcode"></i></button>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($qr['image'])): ?>
                                            <img src="<?= htmlspecialchars($qr['image']) ?>" alt="QR Image" class="qr-image" />
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled title="Geen afbeelding"><i class="fas fa-image"></i></button>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Actions">
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="duplicate_index" value="<?= $index ?>" />
                                                <button type="submit" class="btn btn-sm btn-secondary" title="Dupliceren"><i class="fa-solid fa-copy"></i>➕ </button>
                                            </form>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="delete_index" value="<?= $index ?>" />
                                                <button type="submit" class="btn btn-sm btn-danger" title="Verwijderen" onclick="return confirm('Weet je zeker dat je deze QR code wilt verwijderen?');"><i class="fa-solid fa-trash"></i>🗑️</button>
                                            </form>
                                            <a href="includes/qr-code-edit.php?edit_index=<?= $index ?>" class="btn btn-sm btn-primary" title="Bewerken"><i class="fa-solid fa-pen-to-square"></i>📝</a>
                                            <?php if (!empty($qr['image'])): ?>
                                                <a href="<?= htmlspecialchars($qr['image']) ?>" download class="btn btn-sm btn-success" title="Downloaden"><i class="fa-solid fa-download"></i>📩</a>
                                            <?php elseif (!empty($qr['qr_code'])): ?>
                                                <a href="https://chart.googleapis.com/chart?cht=qr&chs=150x150&chl=<?= urlencode($qr['qr_code']) ?>" download class="btn btn-sm btn-success" title="Downloaden"><i class="fa-solid fa-download"></i></a>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-success" title="Downloaden" disabled><i class="fas fa-download fa-2x"></i>📩</button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>