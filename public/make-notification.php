<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';

use App\Database;

session_start();

$db = Database::getInstance()->getConnection();

// Fetch available tags
$tagStmt = $db->query("SELECT id, name FROM tag ORDER BY name ASC");
$tags = $tagStmt->fetchAll(PDO::FETCH_ASSOC);

// Security check
if (!isset($_SESSION['company_id']) && (!isset($_SESSION['admin']) || !$_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $message = $_POST['message'] ?? '';
    $selectedTags = $_POST['tags'] ?? [];
    
    if ($title && $message) {
        $db->beginTransaction();
        try {
            // 1. Insert Notification
            $stmt = $db->prepare('INSERT INTO notification (title, body_text, expiry_date, fk_employee) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 7 DAY), ?)');
            $stmt->execute([$title, $message, 1]);
            $notificationId = (int)$db->lastInsertId();

            // 2. Add 'Notification' tag automatically
            $notifTagStmt = $db->prepare("SELECT id FROM tag WHERE name = 'Notification' LIMIT 1");
            $notifTagStmt->execute();
            $notifTagId = $notifTagStmt->fetchColumn();
            if ($notifTagId) {
                $db->prepare("INSERT INTO notification_tag (fk_notification, fk_tag) VALUES (?, ?)")->execute([$notificationId, $notifTagId]);
            }

            // 3. Add selected tags
            $tagInsertStmt = $db->prepare("INSERT INTO notification_tag (fk_notification, fk_tag) VALUES (?, ?)");
            foreach ($selectedTags as $tagId) {
                if ((int)$tagId !== (int)$notifTagId) { // Avoid duplicate
                    $tagInsertStmt->execute([$notificationId, $tagId]);
                }
            }

            $db->commit();
            header('Location: index.php?success=1');
            exit;
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
    <title>AED Studios - Notificatie maken</title>
    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="components/bottomnavigation.js"></script>
    <style>
        .make-notification-page {
            padding: 20px;
            padding-top: 60px;
            min-height: 100vh;
            background: #111;
        }
        .form-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1.5px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 24px;
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        .form-control {
            width: 100%;
            background: #fff;
            border: none;
            border-radius: 12px;
            padding: 14px;
            color: #111;
            font-size: 1rem;
            box-sizing: border-box;
        }
        textarea.form-control {
            min-height: 120px;
            resize: none;
        }
        .submit-btn {
            background: var(--primary-app);
            color: white;
            border: none;
            width: 100%;
            padding: 16px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 1rem;
            margin-top: 10px;
            cursor: pointer;
        }
        .cancel-link {
            display: block;
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            margin-top: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body class="dark-theme">
    <div class="make-notification-page">
        <h2 style="color: #fff; text-align: center;">Notificatie maken</h2>
        <p style="color: rgba(255,255,255,0.5); text-align: center; font-size: 0.9rem;">Plaats een nieuwe update in de carousel op de homepage.</p>

        <div class="form-card">
            <form method="POST">
                <div class="form-group">
                    <label>Titel</label>
                    <input type="text" name="title" class="form-control" placeholder="Bijv. Nieuwe Studio Geopend" required>
                </div>
                <div class="form-group">
                    <label>Boodschap</label>
                    <textarea name="message" class="form-control" placeholder="Schrijf hier je bericht..." required></textarea>
                </div>
                <div class="form-group">
                    <label>Tags</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <?php foreach ($tags as $tag): ?>
                            <?php if ($tag['name'] !== 'Notification'): ?>
                                <label style="display: flex; align-items: center; background: rgba(255,255,255,0.05); padding: 10px; border-radius: 10px; cursor: pointer;">
                                    <input type="checkbox" name="tags[]" value="<?= $tag['id'] ?>" style="margin-right: 10px;">
                                    <span style="color: #fff; font-size: 0.9rem;"><?= htmlspecialchars($tag['name']) ?></span>
                                </label>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="submit" class="submit-btn" style="background: #ff1f18;">Versturen</button>
            </form>
        </div>
        <a href="index.php" class="cancel-link">Annuleren</a>
    </div>

    <bottom-navigation></bottom-navigation>
</body>

</html>