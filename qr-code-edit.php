<?php
// Load existing QR codes from JSON file
$jsonFile = __DIR__ . '/qr-codes.json';
$qrCodes = [];
if (file_exists($jsonFile)) {
    $jsonData = file_get_contents($jsonFile);
    $qrCodes = json_decode($jsonData, true) ?? [];
}

$editIndex = $_GET['edit_index'] ?? null;
$editing = false;
$existingData = [
    'title' => '',
    'content' => '',
    'era' => '',
    'qr_code' => '',
    'image' => '',
];

if ($editIndex !== null && isset($qrCodes[$editIndex])) {
    $editing = true;
    $existingData = $qrCodes[$editIndex];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $era = $_POST['era'] ?? '';
    $qrCode = $_POST['qr_code'] ?? '';
    $image = $existingData['image'] ?? '';

    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate QR code image from qr_code link if no image uploaded
    if (empty($image) && !empty($qrCode)) {
        // Generate filename based on sanitized title and timestamp
        $safeTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($title));
        $imageName = $safeTitle . '_' . time() . '.png';
        $targetFile = $uploadDir . $imageName;

        // Google Chart API URL for QR code
        $qrUrl = 'https://chart.googleapis.com/chart?cht=qr&chs=150x150&chl=' . urlencode($qrCode);

        // Fetch the QR code image content
        $imageContent = file_get_contents($qrUrl);
        if ($imageContent !== false) {
            file_put_contents($targetFile, $imageContent);
            $image = 'includes/uploads/' . $imageName;
        }
    }

    // Handle image upload if provided (overwrite generated image)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $imageName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = 'includes/uploads/' . $imageName;
        }
    }

    if ($editing) {
        // Update existing QR code
        $qrCodes[$editIndex] = [
            'title' => $title,
            'content' => $content,
            'era' => $era,
            'qr_code' => $qrCode,
            'image' => $image,
        ];
    } else {
        // Add new QR code data
        $qrCodes[] = [
            'title' => $title,
            'content' => $content,
            'era' => $era,
            'qr_code' => $qrCode,
            'image' => $image,
        ];
    }

    // Save back to JSON file
    file_put_contents($jsonFile, json_encode($qrCodes, JSON_PRETTY_PRINT));

    // Redirect to admin.php after saving
    header('Location: ../admin.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Museum Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.25.1/ui/trumbowyg.min.css" />
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .dashboard-container,
        .edit-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .btn-custom {
            width: 100%;
            font-size: 1.2rem;
            padding: 10px;
        }

        .btn-sm {
            font-size: 1rem;
            padding: 6px 10px;
        }

        .table-container {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <!-- QR-code bewerken pagina -->
    <div class="edit-container">
        <h2 class="text-center">Bewerk Museumstukken</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Titel</label>
                <input type="text" class="form-control" id="title" name="title" required value="<?= htmlspecialchars($existingData['title']) ?>" />
            </div>
            <div class="mb-3">
                <label for="qr_code" class="form-label">QR-code</label>
                <input type="text" class="form-control" id="qr_code" name="qr_code" value="<?= htmlspecialchars($existingData['qr_code']) ?>" />
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Tekst</label>
                <textarea id="content" name="content" class="form-control"><?= htmlspecialchars($existingData['content']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="era">Tijdsperk:</label>
                <select name="era" id="era" class="form-select">
                    <option value="Prehistorie" <?= $existingData['era'] === 'Prehistorie' ? 'selected' : '' ?>>Prehistorie</option>
                    <option value="Oude Nabije Oosten" <?= $existingData['era'] === 'Oude Nabije Oosten' ? 'selected' : '' ?>>Oude Nabije Oosten</option>
                    <option value="Klassieke oudheid" <?= $existingData['era'] === 'Klassieke oudheid' ? 'selected' : '' ?>>Klassieke oudheid</option>
                    <option value="middeleeuwen" <?= $existingData['era'] === 'middeleeuwen' ? 'selected' : '' ?>>middeleeuwen</option>
                    <option value="Vroegmoderne tijd" <?= $existingData['era'] === 'Vroegmoderne tijd' ? 'selected' : '' ?>>Vroegmoderne tijd</option>
                    <option value="Moderne tijd" <?= $existingData['era'] === 'Moderne tijd' ? 'selected' : '' ?>>Moderne tijd</option>
                    <option value="Hedendaagse tijd" <?= $existingData['era'] === 'Hedendaagse tijd' ? 'selected' : '' ?>>Hedendaagse tijd</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Afbeelding</label>
                <input type="file" class="form-control" id="image" name="image" />
            </div>
            <button type="submit" class="btn btn-success w-100"><i class="fas fa-save"></i> Opslaan</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.25.1/trumbowyg.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#content').trumbowyg();
        });
    </script>
</body>

</html>
