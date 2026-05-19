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

$companyId = $_SESSION['company_id'] ?? null;
// In a real app, you would fetch appointments for the logged-in company
// $appointments = $eventRepo->findByCompanyId((int)$companyId);
$appointments = []; // Placeholder

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AED Studios - Aankomende afspraken</title>
    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="components/bottomnavigation.js"></script>
</head>
<body>

    <div class="tabs-container">
        <div class="tabs">
            <a href="events.php" class="tab">Geplande<br>afspraken</a>
            <a href="upcoming-appointments.php" class="tab active">Aankomende<br>afspraken</a>
            <a href="upcoming.php" class="tab">Evenementen</a>
        </div>
    </div>
    
    <h2>Nog te bevestigen afspraken</h2>
    <div class="line"></div>

    <div class="events-list upcoming-list">
        <?php if (empty($appointments)): ?>
            <div class="event-card event-card-upcoming">
                <div class="event-info event-info-upcoming">
                    <h3 class="event-title event-title-upcoming">Geen afspraken om te bevestigen</h3>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($appointments as $app): ?>
                <a href="appointment-detail.php?id=<?= $app->id ?>" style="text-decoration: none; color: inherit;">
                    <div class="event-card event-card-upcoming">
                         <!-- Details here -->
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <bottom-navigation></bottom-navigation>

</body>
</html>