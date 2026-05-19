<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Repository/AbstractRepository.php';
require_once __DIR__ . '/../src/Repository/AccountRepository.php';
require_once __DIR__ . '/../src/Entity/Account.php';

use App\Database;
use App\Repository\AccountRepository;

session_start();

$db = Database::getInstance()->getConnection();
$accountRepo = new AccountRepository($db);

$accountId = $_SESSION['account_id'] ?? null;
$account = $accountId ? $accountRepo->findById((int)$accountId) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $account) {
    $language = $_POST['language'] ?? 'en';
    $notificationsEnabled = isset($_POST['notifications_enabled']) ? 1 : 0;
    $newsletterSubscribed = isset($_POST['newsletter_subscribed']) ? 1 : 0;
    $privacySearchable = isset($_POST['privacy_searchable']) ? 1 : 0;

    $stmt = $db->prepare('UPDATE account SET language = ?, notifications_enabled = ?, newsletter_subscribed = ?, privacy_searchable = ? WHERE id = ?');
    $stmt->execute([$language, $notificationsEnabled, $newsletterSubscribed, $privacySearchable, $account->id]);

    // Refresh account data
    $account = $accountRepo->findById($account->id);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AED Studios - Instellingen</title>
    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="components/bottomnavigation.js"></script>
</head>

<body>
    <h1>Instellingen</h1>
    <div class="line"></div>
    <form method="POST">
        <div class="setting-item">
            <label>Taal</label>
            <div class="setting-control">
                <select name="language" style="width: 100%; padding: 8px; background: #222; color: white; border: 1px solid #444; border-radius: 4px;">
                    <option value="nl" <?= ($account->language ?? '') === 'nl' ? 'selected' : '' ?>>Nederlands</option>
                    <option value="en" <?= ($account->language ?? '') === 'en' ? 'selected' : '' ?>>English</option>
                </select>
            </div>
        </div>
        <div class="setting-item">
            <label>Notificaties</label>
            <div class="setting-control">
                <input type="checkbox" name="notifications_enabled" <?= ($account->notificationsEnabled ?? true) ? 'checked' : '' ?>>
                <span>Inschakelen</span>
            </div>
        </div>
        <div class="setting-item">
            <label>Nieuwsbrief</label>
            <div class="setting-control">
                <input type="checkbox" name="newsletter_subscribed" <?= ($account->newsletterSubscribed ?? false) ? 'checked' : '' ?>>
                <span>Aangemeld</span>
            </div>
        </div>
        <div class="setting-item">
            <label>Privacy (Vindbaarheid)</label>
            <div class="setting-control">
                <input type="checkbox" name="privacy_searchable" <?= ($account->privacySearchable ?? true) ? 'checked' : '' ?>>
                <span>Doorzoekbaar</span>
            </div>
        </div>
        <button type="submit" class="btn">Opslaan</button>
    </form>
    <bottom-navigation></bottom-navigation>
</body>

</html>