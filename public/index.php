<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Repository/AbstractRepository.php';
require_once __DIR__ . '/../src/Repository/NotificationRepository.php';
require_once __DIR__ . '/../src/Entity/Notification.php';

use App\Database;
use App\Repository\NotificationRepository;

session_start();

$db = Database::getInstance()->getConnection();
$notificationRepo = new NotificationRepository($db);

// Fallback for demo if not logged in
$accountId = $_SESSION['account_id'] ?? 1; 
$notifications = $notificationRepo->findActiveByEmployeeId((int)$accountId);

?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AED Studios - Home</title>
    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="components/bottomnavigation.js"></script>
</head>

<body>
    <main class="home-page">
        <section class="home-section" aria-labelledby="notifications-title">
            <div class="home-section-header">
                <h2 id="notifications-title">Notificaties</h2>
                <p>Klik op een notificatie om meer informatie te zien.</p>
            </div>
            <div class="notification-carousel" aria-live="polite">
                <button class="carousel-control carousel-control-prev" id="carouselPrev" type="button" aria-label="Vorige notificatie">‹</button>
                <div class="notification-carousel-frame">
                    <div class="notification-track" id="notificationTrack">
                        <?php if (empty($notifications)): ?>
                            <div class="notification-card" style="background: var(--color-surface-variant);">
                                <div class="notification-content">
                                    <h3 class="notification-title">Geen nieuwe notificaties</h3>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($notifications as $notification): ?>
                                <div class="notification-card" style="background: var(--color-primary-container);">
                                    <div class="notification-content">
                                        <h3 class="notification-title"><?= htmlspecialchars($notification->title) ?></h3>
                                        <p class="notification-body"><?= htmlspecialchars($notification->bodyText) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <button class="carousel-control carousel-control-next" id="carouselNext" type="button" aria-label="Volgende notificatie">›</button>
            </div>
        </section>

        <section class="home-banner" aria-labelledby="home-banner-title">
            <p class="home-eyebrow">AED Studios</p>
            <h1 id="home-banner-title">Visuele communicatie voor bedrijven en theater</h1>
            <p class="home-banner-text">
                Plaats een banner of boodschap op de homepagina om bedrijven extra in de picture te zetten of een theater voorstelling te promoten.
            </p>
            <div class="home-banner-actions">
                <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'company_member' || $_SESSION['role'] === 'admin')): ?>
                    <button class="btn home-create-button btn-small" id="createNotificationButton" type="button">Notificatie maken</button>
                <?php endif; ?>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a class="btn home-banner-edit-button btn-small" href="admin.php">Banner aanpassen</a>
                <?php endif; ?>
            </div>
        </section>

        <div class="home-map-cta">
            <a class="btn home-map-button" href="map.php">
                <img class="home-map-button-icon" src="images/nav-ico/map.svg" alt="" aria-hidden="true">
                <span>Open map</span>
            </a>
        </div>

        <div class="home-map-cta">
            <a class="btn home-map-button" href="companies.php">
                <img class="home-map-button-icon" src="images/nav-ico/grid.svg" alt="" aria-hidden="true">
                <span>Open Bedrijvenlijst</span>
            </a>
        </div>
    </main>

    <bottom-navigation></bottom-navigation>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const track = document.getElementById('notificationTrack');
            const prevBtn = document.getElementById('carouselPrev');
            const nextBtn = document.getElementById('carouselNext');
            const cards = document.querySelectorAll('.notification-card');
            
            if (cards.length <= 1) {
                if(prevBtn) prevBtn.style.display = 'none';
                if(nextBtn) nextBtn.style.display = 'none';
                return;
            }

            let index = 0;

            const updateCarousel = () => {
                const cardWidth = cards[0].offsetWidth + 16; // width + gap
                track.style.transform = `translateX(-${index * cardWidth}px)`;
            };

            nextBtn.addEventListener('click', () => {
                index = (index + 1) % cards.length;
                updateCarousel();
            });

            prevBtn.addEventListener('click', () => {
                index = (index - 1 + cards.length) % cards.length;
                updateCarousel();
            });
        });
    </script>
</body>

</html>