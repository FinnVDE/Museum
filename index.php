
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .dashboard-container {
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
    <div class="dashboard-container">
        <h2 class="text-center">QR-code Beheer</h2>
        <a href="./includes/qr-code-edit.php" class="btn btn-primary btn-custom mb-3">
            <i class="fas fa-plus"></i> Nieuwe QR-code aanmaken
        </a>
        <a href="./admin.php" class="btn btn-secondary btn-custom mb-3 ms-2">
            <i class="fas fa-user-shield"></i> Admin pagina
        </a>
        <div class="table-container">
            <table class="table table-striped text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Naam</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Voorbeeld QR-code</td>
                        <td>
                            <button class='btn btn-success btn-sm me-1' title="Download QR-code"><i class="fas fa-download">Download</i></button>
                            <button class='btn btn-success btn-sm me-1' title="Bewerk QR-code"><i class="fas fa-edit">Bewerk</i></button>
                            <button class='btn btn-success btn-sm me-1' title="Dupliceer QR-code"><i class="fas fa-copy"></i>Dupliceer</button>
                            <button class='btn btn-success btn-sm' title="Verwijder QR-code"><i class="fas fa-trash"></i>Verwijder</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
```
