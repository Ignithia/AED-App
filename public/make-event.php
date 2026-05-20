<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Repository/AbstractRepository.php';
require_once __DIR__ . '/../src/Repository/EventRepository.php';
require_once __DIR__ . '/../src/Repository/PictureRepository.php';
require_once __DIR__ . '/../src/Entity/Event.php';

use App\Database;
use App\Repository\EventRepository;
use App\Repository\PictureRepository;

session_start();

$db = Database::getInstance()->getConnection();
$eventRepo = new EventRepository($db);
$pictureRepo = new PictureRepository($db);

// Fetch tags
$tagStmt = $db->query("SELECT id, name FROM tag ORDER BY name ASC");
$allTags = $tagStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $info = $_POST['info'] ?? '';
    $start = $_POST['start'] ?? '';
    $end = $_POST['end'] ?? '';
    $imageLinks = $_POST['image_links'] ?? [];
    $selectedTags = $_POST['tags'] ?? [];

    if ($name && $info && $start && $end) {
        $db->beginTransaction();
        try {
            $eventId = $eventRepo->create($name, $info, $start, $end, null);

            foreach ($imageLinks as $link) {
                if (!empty(trim($link))) {
                    $pictureRepo->create(trim($link), $eventId);
                }
            }

            // Always add 'Event' tag
            $eventTagQuery = $db->prepare("SELECT id FROM tag WHERE name = 'Event' LIMIT 1");
            $eventTagQuery->execute();
            $eventTagId = $eventTagQuery->fetchColumn();
            if ($eventTagId) {
                $db->prepare("INSERT INTO event_tag (fk_event, fk_tag) VALUES (?, ?)")->execute([$eventId, $eventTagId]);
            }

            // Add selected tags
            $tagInsert = $db->prepare("INSERT INTO event_tag (fk_event, fk_tag) VALUES (?, ?)");
            foreach ($selectedTags as $tagId) {
                if ((int)$tagId !== (int)$eventTagId) {
                    $tagInsert->execute([$eventId, $tagId]);
                }
            }

            $db->commit();
            header('Location: upcoming.php');
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
    <title>AED Studios - Plan</title>
    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="components/bottomnavigation.js"></script>
</head>

<body class="dark-theme">
    <h2 class="make-event-title">Evenement Plannen</h2>
    <form method="POST" class="make-event-form">
        <div class="make-event-field">
            <label>Naam</label>
            <input name="name" type="text" required>
        </div>
        <div class="make-event-field">
            <label>Beschrijving</label>
            <textarea name="info" rows="5" required></textarea>
        </div>
        <div class="make-event-field">
            <label>Begin (YYYY-MM-DD HH:MM)</label>
            <input name="start" type="text" placeholder="2026-05-20 10:00" required>
        </div>
        <div class="make-event-field">
            <label>Einde (YYYY-MM-DD HH:MM)</label>
            <input name="end" type="text" placeholder="2026-05-20 12:00" required>
        </div>

        <div class="make-event-field" id="image-links-container">
            <label>Favoriete Afbeeldingen (Links)</label>
            <div class="image-link-row">
                <input name="image_links[]" type="url" placeholder="https://example.com/image1.jpg">
            </div>
        </div>
        <button type="button" class="btn btn-secondary" onclick="addImageLink()" style="background: #444; margin-bottom: 20px; font-size: 0.8rem;">+ Extra Afbeelding</button>

        <div class="make-event-field">
            <label>Tags</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <?php foreach ($allTags as $tag): ?>
                    <?php if ($tag['name'] !== 'Event' && $tag['name'] !== 'Notification'): ?>
                        <label style="display: flex; align-items: center; background: rgba(255,255,255,0.05); padding: 10px; border-radius: 10px; cursor: pointer;">
                            <input type="checkbox" name="tags[]" value="<?= $tag['id'] ?>" style="margin-right: 10px;">
                            <span style="color: #fff; font-size: 0.9rem;"><?= htmlspecialchars($tag['name']) ?></span>
                        </label>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <button class="make-event-save" type="submit" style="margin-top: 20px;">Opslaan</button>
    </form>

    <script>
        function addImageLink() {
            const container = document.getElementById('image-links-container');
            const newRow = document.createElement('div');
            newRow.className = 'image-link-row';
            newRow.style.marginTop = '8px';
            newRow.innerHTML = '<input name="image_links[]" type="url" placeholder="https://example.com/image.jpg">';
            container.appendChild(newRow);
        }
    </script>
    <bottom-navigation></bottom-navigation>
</body>

</html>