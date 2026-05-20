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

$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$event = $eventRepo->findById($eventId);

if (!$event) {
    header('Location: upcoming.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AED Studios - Detail</title>
    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="components/bottomnavigation.js"></script>
</head>

<body class="dark-theme">
    <h2 class="detail-title"><?= htmlspecialchars($event->eventName) ?></h2>
    <div class="line"></div>
    <div class="detail-content">
        <h3>Info:</h3>
        <p><?= nl2br(htmlspecialchars($event->eventInfo)) ?></p>
        <h3>Details:</h3>
        <p>Datum: <?= $event->startTime->format('d/m/y') ?></p>
        <p>Tijd: <?= $event->startTime->format('H:i') ?> - <?= $event->endTime->format('H:i') ?></p>
    </div>
    <bottom-navigation></bottom-navigation>
</body>

</html>