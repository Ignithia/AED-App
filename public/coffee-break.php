<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Repository/AbstractRepository.php';
require_once __DIR__ . '/../src/Repository/CoffeeBreakRepository.php';
require_once __DIR__ . '/../src/Entity/CoffeeBreak.php';

use App\Database;
use App\Repository\CoffeeBreakRepository;

session_start();

$db = Database::getInstance()->getConnection();
$coffeeRepo = new CoffeeBreakRepository($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $location = $_POST['location'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $time = $_POST['time'] ?? '';
    $accountId = $_SESSION['account_id'] ?? 1; // Default for demo

    if ($location && $reason && $time) {
        $coffeeRepo->create($location, $reason, $time, (int)$accountId);
        header('Location: upcoming-appointments.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AED Studios - Coffee Break</title>
    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="components/bottomnavigation.js"></script>
</head>

<body class="dark-theme">
    <div class="coffee-break-page">
        <div class="coffee-break-header" style="display: flex; align-items: center; justify-content: center; position: relative;">
            <h2 class="coffee-break-page-title" style="margin: 0;">Coffee break</h2>
            <button type="button" class="info-btn" onclick="showCoffeeInfo()" style="position: absolute; right: 0; background: none; border: 2px solid #fff; color: #fff; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: pointer; font-family: serif;">i</button>
        </div>
        <div class="coffee-break-page-line"></div>
        <form method="POST">
            <section class="coffee-break-form-group">
                <label class="coffee-break-label">Locatie</label>
                <select name="location" class="coffee-break-select">
                    <option>Studio 6</option>
                    <option>Studio 12</option>
                </select>
            </section>
            <section class="coffee-break-form-group">
                <label class="coffee-break-label">Reden</label>
                <textarea name="reason" class="coffee-break-textarea" rows="7" required placeholder="Waarom wil je een coffee break?"></textarea>
            </section>
            <section class="coffee-break-form-group" style="margin-bottom: 20px;">
                <label class="coffee-break-label">Tijd (YYYY-MM-DD HH:MM)</label>
                <input type="text" name="time" class="coffee-break-input" placeholder="2026-05-20 10:30" required style="width: 100%; box-sizing: border-box; background: #fff; border: 1.5px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 12px 16px; color: #111; font-size: 0.95rem;">
            </section>
            <div class="coffee-break-actions">
                <button class="coffee-break-primary-action" type="submit">Door gaan</button>
                <a href="index.php" class="coffee-break-secondary-action">Annuleer</a>
            </div>
        </form>
    </div>

    <!-- Info Modal Overlay -->
    <div id="coffeeInfoModal" class="notification-overlay" onclick="closeCoffeeModal()" style="display: none;">
        <div class="notification-overlay-backdrop"></div>
        <div class="notification-overlay-panel" onclick="event.stopPropagation()">
            <button class="overlay-close" onclick="closeCoffeeModal()" style="color: #111;">&times;</button>
            <div class="notification-overlay-label">Informatie</div>
            <h2>Coffee Break</h2>
            <div class="notification-overlay-text">
                Plan een coffee break in met andere bedrijven op de campus. Kies een locatie, geef een reden op en nodig anderen uit om te netwerken.
            </div>
        </div>
    </div>

    <script>
        function showCoffeeInfo() {
            const modal = document.getElementById('coffeeInfoModal');
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('is-open'), 10);
        }

        function closeCoffeeModal() {
            const modal = document.getElementById('coffeeInfoModal');
            modal.classList.remove('is-open');
            setTimeout(() => modal.style.display = 'none', 300);
        }
    </script>
    <bottom-navigation></bottom-navigation>
</body>

</html>