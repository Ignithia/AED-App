<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Repository/AbstractRepository.php';
require_once __DIR__ . '/../src/Repository/EventRepository.php';
require_once __DIR__ . '/../src/Entity/Event.php';

use App\Database;
use App\Repository\EventRepository;

session_start();

$db = Database::getInstance()->getConnection();
$eventRepo = new EventRepository($db);

$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$event = $eventRepo->findById($eventId);

if (!$event) {
    header('Location: events.php');
    exit;
}

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

<body>

    <h2 class="detail-title"><?= htmlspecialchars($event->eventName) ?></h2>
    <div class="line"></div>

    <div class="detail-content">
        <h3 class="detail-section-title">Intro:</h3>
        <p class="detail-text"><?= nl2br(htmlspecialchars($event->eventInfo)) ?></p>

        <h3 class="detail-section-title">Details evenement:</h3>
        <p class="detail-text">Host: AED Studios</p>
        <p class="detail-text">Datum: <?= $event->startTime->format('d/m/y') ?></p>
        <p class="detail-text">Uur: <?= $event->startTime->format('H:i') ?> - <?= $event->endTime->format('H:i') ?></p>
        <p class="detail-text">Locatie: Studio 12</p>

        <h3 class="detail-section-title">Details organisator:</h3>
        <div class="detail-row"><span>Telefoon:</span> <span>01 234 56 78</span></div>
        <div class="detail-row"><span>Contactpersoon:</span> <span>John Doe</span></div>

        <section class="detail-tags-section" aria-labelledby="event-tags-title">
            <h3 class="detail-section-title" id="event-tags-title">Tags:</h3>
            <div class="company-detail-tags-grid">
                <custom-tag name="Productie"></custom-tag>
                <custom-tag name="Networking"></custom-tag>
                <custom-tag name="Creatief"></custom-tag>
                <custom-tag name="Live event"></custom-tag>
            </div>
        </section>

        <div class="detail-action-wrap">
            <button class="detail-cta-button" type="button">Neem deel</button>
        </div>

        <div class="detail-image-divider"></div>
        <div class="detail-event-image-placeholder" aria-hidden="true"></div>
    </div>

    <bottom-navigation></bottom-navigation>

</body>

</html>