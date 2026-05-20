<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Repository/AbstractRepository.php';
require_once __DIR__ . '/../src/Repository/EventRepository.php';
require_once __DIR__ . '/../src/Repository/PictureRepository.php';
require_once __DIR__ . '/../src/Entity/Event.php';
require_once __DIR__ . '/../src/Entity/Picture.php';

use App\Database;
use App\Repository\EventRepository;
use App\Repository\PictureRepository;

session_start();

$db = Database::getInstance()->getConnection();
$eventRepo = new EventRepository($db);
$pictureRepo = new PictureRepository($db);

$companyId = $_SESSION['company_id'] ?? 1; // Default for demo if not logged in
$events = $eventRepo->findByCompanyId((int)$companyId);
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AED Studios - Gepland</title>
    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="components/bottomnavigation.js"></script>
</head>

<body class="dark-theme">
    <div class="tabs-container">
        <div class="tabs">
            <a href="events.php" class="tab active">Geplande</a>
            <?php if (isset($_SESSION['company_id']) || (isset($_SESSION['admin']) && $_SESSION['admin'])): ?>
                <a href="upcoming-appointments.php" class="tab">Aankomende</a>
            <?php endif; ?>
            <a href="upcoming.php" class="tab">Evenementen</a>
        </div>
    </div>
    <h2>Geplande afspraken</h2>
    <div class="line"></div>
    <div class="events-list">
        <?php if (empty($events)): ?>
            <div class="event-card">
                <h3 style="width: 100%; text-align: center; color: #111;">Geen afspraken gevonden</h3>
            </div>
        <?php else: ?>
            <?php foreach ($events as $event): ?>
                <?php $firstImage = $pictureRepo->findFirstByEventId($event->id); ?>
                <a href="event-detail.php?id=<?= $event->id ?>" style="text-decoration: none; color: inherit;">
                    <div class="event-card">
                        <div class="event-img-container">
                            <?php if ($firstImage): ?>
                                <img src="<?= htmlspecialchars($firstImage->url) ?>" alt="Event Cover" class="event-logo" style="object-fit: cover;">
                            <?php else: ?>
                                <img src="images/logo/Home.svg" alt="Logo" class="event-logo">
                            <?php endif; ?>
                        </div>
                        <div class="event-divider"></div>
                        <div class="event-info">
                            <h3 class="event-title"><?= htmlspecialchars($event->eventName) ?></h3>
                            <p class="event-datetime"><?= $event->startTime->format('d/m/y | H:i') ?></p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <bottom-navigation></bottom-navigation>
</body>

</html>