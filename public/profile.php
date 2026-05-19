<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Repository/AbstractRepository.php';
require_once __DIR__ . '/../src/Repository/AccountRepository.php';
require_once __DIR__ . '/../src/Entity/Account.php';
require_once __DIR__ . '/../src/Repository/CompanyRepository.php';
require_once __DIR__ . '/../src/Entity/Company.php';

use App\Database;
use App\Repository\AccountRepository;
use App\Repository\CompanyRepository;

session_start();

$db = Database::getInstance()->getConnection();
$accountRepo = new AccountRepository($db);
$companyRepo = new CompanyRepository($db);

$accountId = $_SESSION['account_id'] ?? null;
$account = $accountId ? $accountRepo->findById((int)$accountId) : null;
$company = $account && $account->companyId ? $companyRepo->findById($account->companyId) : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AED Studios - Profiel</title>
    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="components/bottomnavigation.js"></script>
</head>

<body>
    <div class="profile-header">
        <?php if ($company): ?>
            <img src="images/logo/<?= htmlspecialchars($company->logoUrl ?? 'default-logo.png') ?>" class="profile-logo">
            <h2 class="profile-company-name"><?= htmlspecialchars($company->companyName) ?></h2>
        <?php endif; ?>
    </div>
    <div class="line"></div>
    <div class="profile-info">
        <p><strong>Naam:</strong> <?= htmlspecialchars($account->name ?? 'Gast') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($account->email ?? '-') ?></p>
    </div>
    <div class="profile-actions">
        <a href="edit-company.php" class="btn">Bewerk gegevens</a>
        <a href="settings.php" class="btn">Instellingen</a>
    </div>
    <bottom-navigation></bottom-navigation>
</body>

</html>