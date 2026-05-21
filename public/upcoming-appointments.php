<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Entity/Event.php';
require_once __DIR__ . '/../src/Entity/CoffeeBreak.php';
require_once __DIR__ . '/../src/Repository/AbstractRepository.php';
require_once __DIR__ . '/../src/Repository/EventRepository.php';
require_once __DIR__ . '/../src/Repository/CoffeeBreakRepository.php';

use App\Database;
use App\Repository\EventRepository;
use App\Repository\CoffeeBreakRepository;
use App\Entity\Event;
use App\Entity\CoffeeBreak;

session_start();

$db = Database::getInstance()->getConnection();
$eventRepo = new EventRepository($db);
$coffeeRepo = new CoffeeBreakRepository($db);

if (!isset($_SESSION['company_id']) && (!isset($_SESSION['admin']) || !$_SESSION['admin'])) {
    header('Location: upcoming.php');
    exit;
}

$companyId = isset($_SESSION['company_id']) ? (int) $_SESSION['company_id'] : null;

if ($companyId === null) {
    header('Location: upcoming.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['coffee_id'], $_POST['status'])) {
    $status = $_POST['status'];
    if (in_array($status, ['accepted', 'denied'], true)) {
        $coffeeRepo->updateStatusForCompany((int)$_POST['coffee_id'], $status, $companyId);
    }
    header('Location: upcoming-appointments.php');
    exit;
}

$appointments = $coffeeRepo->findPendingByCompanyId($companyId);
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

<body class="dark-theme">

    <div class="tabs-container">
        <div class="tabs">
            <a href="events.php" class="tab">Geplande</a>
            <?php if (isset($_SESSION['company_id']) || (isset($_SESSION['admin']) && $_SESSION['admin'])): ?>
                <a href="upcoming-appointments.php" class="tab active">Aankomende</a>
            <?php endif; ?>
            <a href="upcoming.php" class="tab">Evenementen</a>
        </div>
    </div>

    <h2>Nog te bevestigen afspraken</h2>
    <div class="line"></div>

    <div class="events-list upcoming-list">
        <?php if (empty($appointments)): ?>
            <div class="event-card">
                <h3 style="width: 100%; text-align: center; color: var(--white);">Geen verzoeken gevonden</h3>
            </div>
        <?php else: ?>
            <?php foreach ($appointments as $app): ?>
                <div class="event-card" style="display: flex; flex-direction: column; padding: 15px; height: auto;">
                    <div style="display: flex; width: 100%; align-items: center; margin-bottom: 12px;">
                        <div class="event-img-container" style="flex-shrink: 0;">
                            <img src="images/logo/coffee.svg" alt="Coffee" class="event-logo">
                        </div>
                        <div class="event-divider" style="background: #6f4e37; height: 40px; margin: 0 15px;"></div>
                        <div class="event-info" style="flex: 1;">
                            <h3 class="event-title" style="margin: 0; font-size: 1rem; color: #111;">Koffiepauze: <?= htmlspecialchars($app->reason) ?></h3>
                            <p class="event-datetime" style="margin: 4px 0 0; color: #444;"><?= htmlspecialchars($app->location) ?></p>
                            <p class="event-datetime" style="margin: 2px 0 0; color: #666; font-size: 0.85rem;">
                                <?php 
                                $ts = $app->dateTime ? strtotime($app->dateTime) : false;
                                echo ($ts !== false && $ts > 0) ? date('d/m/y | H:i', $ts) : 'TBD';
                                ?>
                            </p>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 8px; width: 100%; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 12px;">
                        <form method="POST" style="flex: 1; margin: 0;">
                            <input type="hidden" name="coffee_id" value="<?= $app->id ?>">
                            <input type="hidden" name="status" value="accepted">
                            <button type="submit" class="coffee-btn-confirm" style="width: 100%; height: 40px; border-radius: 20px;">Bevestig</button>
                        </form>
                        <form method="POST" style="flex: 1; margin: 0;">
                            <input type="hidden" name="coffee_id" value="<?= $app->id ?>">
                            <input type="hidden" name="status" value="denied">
                            <button type="submit" class="coffee-btn-deny" style="width: 100%; height: 40px; border-radius: 20px; background: rgba(0,0,0,0.05); color: #111; border: 1px solid rgba(0,0,0,0.1);">Weiger</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <bottom-navigation></bottom-navigation>

</body>

</html>