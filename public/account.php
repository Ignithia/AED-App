<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Repository/AbstractRepository.php';
require_once __DIR__ . '/../src/Repository/AccountRepository.php';
require_once __DIR__ . '/../src/Repository/CompanyRepository.php';
require_once __DIR__ . '/../src/Entity/Account.php';
require_once __DIR__ . '/../src/Entity/Company.php';

use App\Database;
use App\Repository\AccountRepository;
use App\Repository\CompanyRepository;

session_start();

$db = Database::getInstance()->getConnection();
$accountRepo = new AccountRepository($db);
$companyRepo = new CompanyRepository($db);

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['guest_login'])) {
        $email = $_POST['email'] ?? '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Ongeldig emailadres.";
        } else {
            $account = $accountRepo->getOrCreateGuestByEmail($email);

            if ($account->companyId !== null) {
                $error = "Dit emailadres is gekoppeld aan een bedrijf. Log in via je bedrijfsaccount.";
            } else {
                $_SESSION['account_id'] = $account->id;
                $_SESSION['email'] = $account->email;
                $_SESSION['role'] = 'guest';

                header('Location: index.php');
                exit;
            }
        }
    } elseif (isset($_POST['company_code']) || isset($_POST['account_code'])) {
        $companyCode = $_POST['company_code'] ?? '';
        $accountCode = $_POST['account_code'] ?? '';

        // Logic for individual account login regardless of company code
        $account = $accountRepo->findByCode($accountCode);

        if ($account) {
            $company = $account->companyId ? $companyRepo->findById($account->companyId) : null;

            $_SESSION['account_id'] = $account->id;
            $_SESSION['company_id'] = $company ? $company->id : null;
            $_SESSION['email'] = $account->email ?? null;
            $_SESSION['role'] = $account->role;

            header('Location: index.php');
            exit;
        }
        $error = "Ongeldige accountcode.";
    }
}

$loggedInAccount = null;
$loggedInCompany = null;

if (isset($_SESSION['account_id'])) {
    $loggedInAccount = $accountRepo->findById((int)$_SESSION['account_id']);
    if ($loggedInAccount && $loggedInAccount->companyId) {
        $loggedInCompany = $companyRepo->findById($loggedInAccount->companyId);
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: account.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AED Studios - Account</title>
    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="components/bottomnavigation.js"></script>
</head>

<body class="dark-theme">
    <main class="account-page">
        <section class="account-hero">
            <p class="account-eyebrow">AED Studios</p>
            <h1 class="account-title">Account</h1>
        </section>

        <?php if (!$loggedInAccount && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guest')): ?>
            <section class="account-card account-login-card">
                <h2>Login als gast</h2>
                <p class="account-card-text">Je bent nog niet ingelogd. Kies een type account om verder te gaan.</p>
                <form method="POST">
                    <div class="account-actions-row">
                        <input type="hidden" name="guest_login" value="1">
                        <label class="account-field">
                            <span>e-mail</span>
                            <input name="email" type="text" required placeholder="BV: aed.studios@example.com">
                        </label>
                        <button class="btn primary" type="submit">Inloggen als gast</button>
                    </div>
                </form>
            </section>

            <section class="account-card account-login-card">
                <h2>Login als bedrijf</h2>
                <?php if ($error): ?>
                    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
                <p class="account-card-text">Vul je codes in om toegang te krijgen tot je bedrijfsomgeving.</p>
                <form method="POST" class="account-login-grid">
                    <label class="account-field">
                        <span>Bedrijfs Code</span>
                        <input name="company_code" type="text" required placeholder="BV: AED001">
                    </label>
                    <label class="account-field">
                        <span>Account Code</span>
                        <input name="account_code" type="password" required placeholder="****">
                    </label>
                    <div class="account-actions-row">
                        <button class="btn primary" type="submit">Inloggen</button>
                    </div>
                </form>
            </section>
        <?php elseif ($loggedInAccount): ?>
            <section class="account-card account-session-card">
                <div class="account-session-header">
                    <?php
                    $logoPath = $loggedInCompany && !empty($loggedInCompany->logo) && $loggedInCompany->logo !== 'default'
                        ? $loggedInCompany->logo
                        : 'images/person-pfp.png';
                    ?>
                    <img class="account-avatar" src="<?= htmlspecialchars($logoPath) ?>" alt="Profielfoto">
                    <div class="account-session-details">
                        <p class="account-session-eyebrow">U bent momenteel ingelogd als:</p>
                        <div class="account-session-name-row">
                            <span class="account-session-name"><?= htmlspecialchars($loggedInAccount->name) ?></span>
                            <?php if ($loggedInCompany): ?>
                                <span class="account-session-divider" aria-hidden="true">|</span>
                                <span class="account-session-role"><?= htmlspecialchars($loggedInCompany->companyName) ?></span>
                            <?php elseif ($loggedInAccount->role === 'guest'): ?>
                                <span class="account-session-divider" aria-hidden="true">|</span>
                                <span class="account-session-role">Bezoeker</span>
                            <?php endif; ?>
                        </div>
                        <p class="account-session-email"><?= htmlspecialchars($loggedInAccount->email) ?></p>
                    </div>
                </div>

                <div class="account-actions-row account-actions-stack">
                    <a class="btn" href="settings.php">Instellingen</a>
                    <?php if ($loggedInAccount->role === 'admin' || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin')): ?>
                        <a class="btn account-admin-button" href="admin.php">Banner aanpassen</a>
                    <?php endif; ?>
                    <?php if ($loggedInAccount->role === 'admin' || $loggedInAccount->role === 'company'): ?>
                        <a class="btn account-admin-button" href="manage-companies.php">Bedrijven Beheren</a>
                    <?php endif; ?>
                    <a href="?logout=1" class="btn primary">Uitloggen</a>
                </div>
            </section>
        <?php endif; ?>
    </main>
    <bottom-navigation></bottom-navigation>
</body>

</html>