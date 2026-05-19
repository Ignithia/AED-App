<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Repository/AbstractRepository.php';
require_once __DIR__ . '/../src/Repository/CompanyRepository.php';
require_once __DIR__ . '/../src/Entity/Company.php';

use App\Database;
use App\Repository\CompanyRepository;

$db = Database::getInstance()->getConnection();
$companyRepo = new CompanyRepository($db);
$companies = $companyRepo->findAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AED Studios - Bedrijven</title>
    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="components/bottomnavigation.js"></script>
</head>

<body>
    <h1>Bedrijven Lijst</h1>
    <div class="line"></div>
    <div class="company-list">
        <?php if (empty($companies)): ?>
            <div class="empty-state">
                <p>Geen bedrijven gevonden op dit moment.</p>
            </div>
        <?php else: ?>
            <?php foreach ($companies as $company): ?>
                <a href="company-detail.php?id=<?= $company->id ?>">
                    <div class="company-item">
                        <img src="images/logo/<?= htmlspecialchars($company->logoUrl ?? 'default-logo.png') ?>" class="company-list-img">
                        <div class="divider-company-list"></div>
                        <h2 class="company-name"><?= htmlspecialchars($company->companyName) ?></h2>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <bottom-navigation></bottom-navigation>
</body>

</html>