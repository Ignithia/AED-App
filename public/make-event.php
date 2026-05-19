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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $info = $_POST['info'] ?? '';
    $start = $_POST['start'] ?? '';
    $end = $_POST['end'] ?? '';

    if ($name && $info && $start && $end) {
        $eventRepo->create($name, $info, $start, $end, null);
        header('Location: upcoming.php');
        exit;
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

<body>
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
        <button class="make-event-save" type="submit">Opslaan</button>
    </form>
    <bottom-navigation></bottom-navigation>
</body>

</html>