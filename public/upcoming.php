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

// Public events (no company ID)
$statement = $db->query('SELECT * FROM event WHERE fk_company IS NULL ORDER BY start_time ASC');
$events = array_map(fn($row) => \App\Entity\Event::fromRow($row), $statement->fetchAll());
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AED Studios - Evenementen</title>
    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="components/bottomnavigation.js"></script>
</head>

<body>
    <div class="tabs-container">
        <div class="tabs">
            <a href="events.php" class="tab">Geplande</a>
            <a href="upcoming-appointments.php" class="tab">Aankomende</a>
            <a href="upcoming.php" class="tab active">Evenementen</a>
        </div>
    </div>
    <h2>Beschikbare evenementen</h2>
    <div class="events-list">
        <?php if (empty($events)): ?>
            <div class="empty-state">
                <p>Er zijn momenteel geen openbare evenementen beschikbaar.</p>
            </div>
        <?php else: ?>
            <?php foreach ($events as $event): ?>
                <div class="event-card">
                    <h3><?= htmlspecialchars($event->eventName) ?></h3>
                    <p><?= $event->startTime->format('d/m/y H:i') ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="events-cta-wrap">
        <a href="make-event.php" class="btn primary">Plan evenement</a>
    </div>
    <bottom-navigation></bottom-navigation>
</body>

</html>