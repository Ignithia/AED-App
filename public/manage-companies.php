<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Repository/AbstractRepository.php';
require_once __DIR__ . '/../src/Repository/CompanyRepository.php';
require_once __DIR__ . '/../src/Entity/Company.php';

use App\Database;
use App\Repository\CompanyRepository;

session_start();

// Admin check
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    // header('Location: index.php');
    // exit;
}

$db = Database::getInstance()->getConnection();
$companyRepo = new CompanyRepository($db);
$companies = $companyRepo->findAll();

?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AED Admin - Bedrijven Beheren</title>
    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="components/bottomnavigation.js"></script>
    <style>
        .manage-companies-page {
            padding: 20px;
            padding-top: 60px;
            min-height: 100vh;
            background: #111;
        }
        .admin-header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .btn-add {
            background: var(--primary-app);
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .company-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .company-admin-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1.5px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .company-admin-info h3 {
            margin: 0;
            color: #fff;
            font-size: 1.1rem;
        }
        .company-admin-info p {
            margin: 4px 0 0;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
        }
        .btn-edit {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 8px 16px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 0.85rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body class="dark-theme">
    <div class="manage-companies-page">
        <div class="admin-header-actions">
            <h2 style="color: #fff; margin: 0;">Bedrijven</h2>
            <a href="add-company.php" class="btn-add">+ Nieuw Bedrijf</a>
        </div>

        <div class="company-list">
            <?php foreach ($companies as $company): ?>
                <div class="company-admin-card">
                    <div class="company-admin-info">
                        <h3><?= htmlspecialchars($company->companyName) ?></h3>
                        <p>Code: <?= htmlspecialchars($company->code) ?></p>
                    </div>
                    <a href="edit-company.php?id=<?= $company->id ?>" class="btn-edit">Bewerken</a>
                </div>
            <?php endforeach; ?>
        </div>

        <a href="admin.php" style="display: block; text-align: center; color: rgba(255,255,255,0.5); text-decoration: none; margin-top: 30px; font-size: 0.9rem;">Terug naar Admin Paneel</a>
    </div>

    <bottom-navigation></bottom-navigation>
</body>

</html>