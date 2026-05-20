<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Repository/AbstractRepository.php';
require_once __DIR__ . '/../src/Repository/CompanyRepository.php';
require_once __DIR__ . '/../src/Repository/AccountRepository.php';
require_once __DIR__ . '/../src/Entity/Company.php';
require_once __DIR__ . '/../src/Entity/Account.php';

use App\Database;
use App\Repository\CompanyRepository;
use App\Repository\AccountRepository;

session_start();

// Admin check
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    // header('Location: index.php');
    // exit;
}

$db = Database::getInstance()->getConnection();
$companyRepo = new CompanyRepository($db);
$accountRepo = new AccountRepository($db);

$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $companyName = $_POST['company_name'] ?? '';
    $companyCode = $_POST['company_code'] ?? '';
    $spokesPerson = $_POST['spokes_person'] ?? '';
    $email = $_POST['email'] ?? '';
    $subAccountsCount = (int)($_POST['sub_accounts_count'] ?? 0);
    $subAccountsCode = $_POST['sub_accounts_code'] ?? ''; // Fixed code for subs if desired

    if ($companyName && $companyCode) {
        $db->beginTransaction();
        try {
            // 1. Create the company
            $companyId = $companyRepo->create(
                $companyName,
                $companyCode,
                'images/logo/default.svg', // Default logo
                'Nieuw bedrijf op de campus.',
                $spokesPerson,
                false, // admin
                false, // private
                $email
            );

            // 2. Create sub-accounts
            if ($subAccountsCount > 0) {
                $accountRepo->bulkCreateForCompany($companyId, $subAccountsCount, $subAccountsCode, $companyName);
            }

            $db->commit();
            $successMsg = "Bedrijf '$companyName' succesvol aangemaakt met $subAccountsCount sub-accounts!";
        } catch (Exception $e) {
            $db->rollBack();
            $error = $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AED Admin - Bedrijf Toevoegen</title>
    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="components/bottomnavigation.js"></script>
    <style>
        .admin-add-company {
            padding: 24px;
            padding-top: 60px;
            min-height: 100vh;
            background: #111;
        }
        .admin-form-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1.5px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 24px;
            margin-top: 24px;
        }
        .admin-input-group {
            margin-bottom: 18px;
        }
        .admin-input-group label {
            display: block;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 8px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .admin-input {
            width: 100%;
            background: #fff;
            border: none;
            border-radius: 12px;
            padding: 14px;
            color: #111;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .admin-submit-btn {
            background: var(--primary-app);
            color: white;
            border: none;
            width: 100%;
            padding: 16px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 12px;
        }
        .success-banner {
            background: #4CAF50;
            color: white;
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
        }
        .error-banner {
            background: #f44336;
            color: white;
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>

<body class="dark-theme">
    <div class="admin-add-company">
        <h2 style="color: #fff; text-align: center; margin: 0;">Bedrijf Toevoegen</h2>
        <p style="color: rgba(255,255,255,0.5); text-align: center; font-size: 0.9rem; margin-top: 8px;">Maak een nieuw bedrijfsaccount en genereer sub-accounts.</p>

        <?php if ($successMsg): ?>
            <div class="success-banner"><?= $successMsg ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="error-banner"><?= $error ?></div>
        <?php endif; ?>

        <div class="admin-form-card">
            <form method="POST">
                <div class="admin-input-group">
                    <label>Bedrijfsnaam</label>
                    <input type="text" name="company_name" class="admin-input" placeholder="Bijv. Red Star Studios" required>
                </div>
                <div class="admin-input-group">
                    <label>Hoofd Login Code</label>
                    <input type="text" name="company_code" class="admin-input" placeholder="Bijv. RS-2026" required>
                </div>
                <div class="admin-input-group">
                    <label>Contactpersoon</label>
                    <input type="text" name="spokes_person" class="admin-input" placeholder="Naam van de beheerder">
                </div>
                <div class="admin-input-group">
                    <label>E-mail</label>
                    <input type="email" name="email" class="admin-input" placeholder="company@example.com">
                </div>

                <div style="margin: 30px 0; height: 1.5px; background: rgba(255,255,255,0.1);"></div>
                
                <h3 style="color: #fff; font-size: 1.1rem; margin-bottom: 15px;">Sub-accounts</h3>

                <div class="admin-input-group">
                    <label>Aantal Sub-accounts</label>
                    <input type="number" name="sub_accounts_count" class="admin-input" value="5" min="0" max="50">
                </div>
                <div class="admin-input-group">
                    <label>Gedeelde Login Code (Optioneel)</label>
                    <input type="text" name="sub_accounts_code" class="admin-input" placeholder="Leeg laten voor unieke codes">
                    <p style="color: rgba(255,255,255,0.4); font-size: 0.75rem; margin-top: 6px;">Als je dit invult, kunnen alle medewerkers met dezelfde code inloggen.</p>
                </div>

                <button type="submit" class="admin-submit-btn" style="background: #ff1f18;">Bedrijf Opslaan</button>
            </form>
        </div>

        <a href="manage-companies.php" style="display: block; text-align: center; color: rgba(255,255,255,0.5); text-decoration: none; margin-top: 24px;">Terug naar Bedrijvenlijst</a>
    </div>

    <bottom-navigation></bottom-navigation>
</body>

</html>