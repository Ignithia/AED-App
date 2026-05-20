<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Repository/AbstractRepository.php';
require_once __DIR__ . '/../src/Repository/EventRepository.php';
require_once __DIR__ . '/../src/Repository/PictureRepository.php';
require_once __DIR__ . '/../src/Repository/TagRepository.php';
require_once __DIR__ . '/../src/Entity/Event.php';
require_once __DIR__ . '/../src/Entity/Tag.php';
require_once __DIR__ . '/../src/Entity/Picture.php';

use App\Database;
use App\Repository\EventRepository;
use App\Repository\PictureRepository;
use App\Repository\TagRepository;

session_start();

$db = Database::getInstance()->getConnection();
$eventRepo = new EventRepository($db);
$pictureRepo = new PictureRepository($db);
$tagRepo = new TagRepository($db);

$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_event'])) {
    $accountId = $_SESSION['account_id'] ?? null;
    if ($accountId) {
        $eventRepo->addParticipant($eventId, (int)$accountId);
        header("Location: events.php");
        exit;
    } else {
        // Fallback for demo if not logged in
        $eventRepo->addParticipant($eventId, 1); 
        header("Location: events.php");
        exit;
    }
}

$event = $eventRepo->findById($eventId);

if (!$event) {
    header('Location: events.php');
    exit;
}

$images = $pictureRepo->findByEventId($eventId);
$tags = $tagRepo->findByEventId($eventId);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AED Studios - <?= htmlspecialchars($event->eventName) ?></title>

    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">

    <script src="components/bottomnavigation.js"></script>
    <script src="components/tag.js"></script>

</head>

<body class="dark-theme">

    <h2 class="detail-title"><?= htmlspecialchars($event->eventName) ?></h2>
    <div class="line"></div>

    <div class="detail-content">
        <h3 class="detail-section-title">Intro:</h3>
        <p class="detail-text"><?= nl2br(htmlspecialchars($event->eventInfo)) ?></p>

        <h3 class="detail-section-title">Details evenement:</h3>
        <p class="detail-text">Host: <?= htmlspecialchars($event->companyName ?? 'AED Studios') ?></p>
        <p class="detail-text">Datum: <?= $event->startTime->format('d/m/y') ?></p>
        <p class="detail-text">Uur: <?= $event->startTime->format('H:i') ?> - <?= $event->endTime->format('H:i') ?></p>
        <p class="detail-text">Locatie: Studio 12</p>

        <h3 class="detail-section-title">Details organisator:</h3>
        <div class="detail-row"><span>Telefoon:</span> <span><?= htmlspecialchars($event->companyPhone ?? '01 234 56 78') ?></span></div>
        <div class="detail-row"><span>Contactpersoon:</span> <span><?= htmlspecialchars($event->spokesPerson ?? 'AED Studios') ?></span></div>

        <section class="detail-tags-section" aria-labelledby="event-tags-title">
            <h3 class="detail-section-title" id="event-tags-title">Tags:</h3>
            <div class="company-detail-tags-grid">
                <?php if (empty($tags)): ?>
                    <p class="detail-text" style="opacity: 0.5;">Geen tags voor dit evenement.</p>
                <?php else: ?>
                    <?php foreach ($tags as $tag): ?>
                        <custom-tag name="<?= htmlspecialchars($tag->name) ?>"></custom-tag>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <div class="detail-action-wrap">
            <form method="POST">
                <button class="detail-cta-button" type="submit" name="join_event">Neem deel</button>
            </form>
        </div>

        <?php if (!empty($images)): ?>
            <div class="detail-image-divider"></div>
            <div class="detail-gallery" style="display: flex; flex-direction: column; gap: 16px; margin-top: 20px;">
                <?php foreach ($images as $img): ?>
                    <img src="<?= htmlspecialchars($img->url) ?>" alt="Event afbeelding" style="width: 100%; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="detail-image-divider"></div>
            <div class="detail-event-image-placeholder" aria-hidden="true"></div>
        <?php endif; ?>
    </div>

    <bottom-navigation></bottom-navigation>

</body>

</html>