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

if (!$accountId) {
    header('Location: index.php');
    exit;
}

$account = $accountRepo->findById((int)$accountId);

// Fetch all tags
$allTagsStmt = $db->query("SELECT id, name FROM tag ORDER BY name ASC");
$allTags = $allTagsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user interests
$userInterestsStmt = $db->prepare("SELECT fk_tag FROM interest WHERE fk_account = ?");
$userInterestsStmt->execute([$accountId]);
$userInterests = $userInterestsStmt->fetchAll(PDO::FETCH_COLUMN);

if (!$account) {
    echo "Account niet gevonden. Log opnieuw in.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $account) {
    $language = $_POST['language'] ?? 'nl';
    $profileVisibility = $_POST['profile_visibility'] ?? 'public';
    $pushNotifications = isset($_POST['push_notifications']) ? 1 : 0;
    $emailNotifications = isset($_POST['email_notifications']) ? 1 : 0;
    $eventPopups = isset($_POST['event_popups']) ? 1 : 0;
    $autoplayVideos = isset($_POST['autoplay_videos']) ? 1 : 0;
    $blockPopups = isset($_POST['block_popups']) ? 1 : 0;
    $audioMuted = isset($_POST['audio_muted']) ? 1 : 0;

    // Handle Interests
    $selectedInterests = $_POST['interests'] ?? [];
    $db->prepare("DELETE FROM interest WHERE fk_account = ?")->execute([$accountId]);
    $interestInsert = $db->prepare("INSERT INTO interest (fk_account, fk_tag) VALUES (?, ?)");
    foreach ($selectedInterests as $tagId) {
        $interestInsert->execute([$accountId, $tagId]);
    }

    // Legacy settings (optional)
    $notificationsEnabled = (isset($_POST['push_notifications']) || isset($_POST['email_notifications'])) ? 1 : 0;
    $privacySearchable = ($profileVisibility === 'public') ? 1 : 0;

    $stmt = $db->prepare('UPDATE account SET 
        language = ?, 
        profile_visibility = ?,
        push_notifications = ?,
        email_notifications = ?,
        event_popups = ?,
        autoplay_videos = ?,
        block_popups = ?,
        audio_muted = ?,
        notifications_enabled = ?,
        privacy_searchable = ?
        WHERE id = ?');

    $stmt->execute([
        $language,
        $profileVisibility,
        $pushNotifications,
        $emailNotifications,
        $eventPopups,
        $autoplayVideos,
        $blockPopups,
        $audioMuted,
        $notificationsEnabled,
        $privacySearchable,
        $account->id
    ]);

    // Refresh account data
    $account = $accountRepo->findById($account->id);
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AED Studios - Instellingen</title>
    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="components/bottomnavigation.js"></script>
</head>

<body class="dark-theme">
    <main class="settings-page" style="padding: 18px 18px 110px;">
        <h1 class="settings-title">Instellingen</h1>
        <div class="settings-line"></div>

        <form method="POST">
            <!-- Language Card -->
            <div class="setting-item" style="flex-direction: column; align-items: flex-start; gap: 8px;">
                <h3 style="margin: 0; font-size: 1.2rem; color: #ffffff;">Taal</h3>
                <div class="setting-control" style="width: 100%;">
                    <select name="language" class="settings-select" style="width: 100%;">
                        <option value="nl" <?= $account->language === 'nl' ? 'selected' : '' ?>>Nederlands</option>
                        <option value="fr" <?= $account->language === 'fr' ? 'selected' : '' ?>>Français</option>
                        <option value="en" <?= $account->language === 'en' ? 'selected' : '' ?>>English</option>
                        <option value="de" <?= $account->language === 'de' ? 'selected' : '' ?>>Deutsch</option>
                    </select>
                </div>
            </div>

            <!-- Privacy Card -->
            <div class="setting-item" style="flex-direction: column; align-items: flex-start; gap: 8px;">
                <h3 style="margin: 0; font-size: 1.2rem; color: #ffffff;">Privacy</h3>
                <div class="setting-control" style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                    <span class="settings-label">Profiel zichtbaar als</span>
                    <select name="profile_visibility" class="settings-select">
                        <option value="public" <?= $account->profileVisibility === 'public' ? 'selected' : '' ?>>Openbaar</option>
                        <option value="private" <?= $account->profileVisibility === 'private' ? 'selected' : '' ?>>Privé</option>
                    </select>
                </div>
            </div>

            <!-- Notifications Card -->
            <div class="setting-item" style="flex-direction: column; align-items: flex-start; gap: 12px;">
                <h3 style="margin: 0; font-size: 1.2rem; color: #ffffff;">Notificaties</h3>
                <div class="setting-control" style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                    <span class="settings-label">Ontvang push-notificaties</span>
                    <input type="checkbox" name="push_notifications" <?= $account->pushNotifications ? 'checked' : '' ?>>
                </div>
                <div class="setting-control" style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                    <span class="settings-label">E-mailmeldingen</span>
                    <input type="checkbox" name="email_notifications" <?= $account->emailNotifications ? 'checked' : '' ?>>
                </div>
                <div class="setting-control" style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                    <span class="settings-label">Evenement pop-ups</span>
                    <input type="checkbox" name="event_popups" <?= $account->eventPopups ? 'checked' : '' ?>>
                </div>
            </div>

            <!-- Media Card -->
            <div class="setting-item" style="flex-direction: column; align-items: flex-start; gap: 12px;">
                <h3 style="margin: 0; font-size: 1.2rem; color: #ffffff;">Media</h3>
                <div class="setting-control" style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                    <span class="settings-label">Auto play video's</span>
                    <input type="checkbox" name="autoplay_videos" <?= $account->autoplayVideos ? 'checked' : '' ?>>
                </div>
                <div class="setting-control" style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                    <span class="settings-label">Blokkeer pop-ups</span>
                    <input type="checkbox" name="block_popups" <?= $account->blockPopups ? 'checked' : '' ?>>
                </div>
            </div>

            <!-- Audio Card -->
            <div class="setting-item" style="flex-direction: column; align-items: flex-start; gap: 12px;">
                <h3 style="margin: 0; font-size: 1.2rem; color: #ffffff;">Audio</h3>
                <div class="setting-control" style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                    <span class="settings-label">Audio uit</span>
                    <input type="checkbox" name="audio_muted" <?= $account->audioMuted ? 'checked' : '' ?>>
                </div>
            </div>

            <!-- Interests Card -->
            <div class="setting-item" style="flex-direction: column; align-items: flex-start; gap: 12px; margin-top: 20px;">
                <h3 style="margin: 0; font-size: 1.2rem; color: #ffffff;">Mijn Interesses</h3>
                <p style="color: rgba(255,255,255,0.5); font-size: 0.9rem; margin: 0;">Selecteer tags om persoonlijke meldingen te ontvangen.</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; width: 100%;">
                    <?php foreach ($allTags as $tag): ?>
                        <label style="display: flex; align-items: center; background: rgba(255,255,255,0.05); padding: 10px; border-radius: 12px; cursor: pointer; border: 1.5px solid <?= in_array($tag['id'], $userInterests) ? 'var(--primary-app)' : 'rgba(255,255,255,0.1)' ?>;">
                            <input type="checkbox" name="interests[]" value="<?= $tag['id'] ?>" <?= in_array($tag['id'], $userInterests) ? 'checked' : '' ?> style="margin-right: 10px;">
                            <span style="color: #fff; font-size: 0.85rem;"><?= htmlspecialchars($tag['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn" style="margin-top: 30px; background: var(--primary-app); color: #fff; width: 100%; padding: 16px; border-radius: 30px; font-weight: 700; border: none;">Instellingen Opslaan</button>
        </form>

        <div class="setting-item" style="margin-top: 30px; border-color: rgba(255,13,13,0.3); flex-direction: column; align-items: flex-start;">
            <h3 style="margin: 0; font-size: 1.2rem; color: #ff1f1f;">Account acties</h3>
            <div style="display: flex; gap: 10px; width: 100%; margin-top: 15px;">
                <button class="btn btn-secondary" style="font-size: 0.8rem; height: 40px; padding: 0;">Download gegevens</button>
                <button class="btn btn-secondary" style="border-color: #ff1f1f; color: #ff1f1f; font-size: 0.8rem; height: 40px; padding: 0;">Verwijder account</button>
            </div>
        </div>
    </main>
    <bottom-navigation></bottom-navigation>
</body>

</html>
<?php /* Final closing moved here */ ?>