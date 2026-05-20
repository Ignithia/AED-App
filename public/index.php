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

$showInterested = isset($_GET['interested']) && $_GET['interested'] == 1 && isset($_SESSION['account_id']);
if ($showInterested) {
    // Get account interests
    $interestStmt = $db->prepare("SELECT fk_tag FROM interest WHERE fk_account = ?");
    $interestStmt->execute([$_SESSION['account_id']]);
    $tagIds = $interestStmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($tagIds)) {
        $notifications = $notificationRepo->findByTags($tagIds);
    } else {
        $notifications = [];
    }
} else {
    $notifications = $notificationRepo->findAllActive();
}
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

<body class="dark-theme">
    <main class="home-page">
        <section class="home-section" aria-labelledby="notifications-title">
            <div class="home-section-header" style="display: flex; justify-content: space-between; align-items: flex-end;">
                <div>
                    <h2 id="notifications-title">Notificaties</h2>
                </div>
                <?php if (isset($_SESSION['account_id'])): ?>
                    <div class="interest-filter">
                        <?php if ($showInterested): ?>
                            <a href="index.php" class="btn" style="background: rgba(255,255,255,0.1); color: #fff; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; text-decoration: none; border: 1.5px solid var(--primary-app);">Toon alles</a>
                        <?php else: ?>
                            <a href="index.php?interested=1" class="btn" style="background: var(--primary-app); color: #fff; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; text-decoration: none;">Interesses</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="notification-carousel" aria-live="polite">
                <div class="notification-carousel-frame">
                    <div class="notification-track" id="notificationTrack">
                        <?php if (empty($notifications)): ?>
                            <div class="notification-card" style="width: 100%; justify-content: center;">
                                <div class="notification-card-body">
                                    <h3 style="text-align: center; margin: 0;">Geen nieuwe notificaties</h3>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($notifications as $notification): ?>
                                <div class="notification-card"
                                    onclick="showNotificationDetails('<?= addslashes(htmlspecialchars($notification->title)) ?>', '<?= addslashes(htmlspecialchars($notification->bodyText)) ?>')">
                                    <div class="notification-card-accent" style="background-color: var(--color-primary);"></div>
                                    <div class="notification-card-body">
                                        <h3><?= htmlspecialchars($notification->title) ?></h3>
                                        <p><?= htmlspecialchars($notification->bodyText) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="carousel-controls-container">
                    <button class="carousel-control carousel-control-prev" id="carouselPrev" type="button" aria-label="Vorige notificatie">‹</button>
                    <button class="carousel-control carousel-control-next" id="carouselNext" type="button" aria-label="Volgende notificatie">›</button>
                </div>
            </div>
        </section>

        <!-- Notification Modal Overlay -->
        <div id="notificationModal" class="notification-overlay" onclick="closeNotificationModal()">
            <div class="notification-overlay-backdrop"></div>
            <div class="notification-overlay-panel" onclick="event.stopPropagation()">
                <button class="overlay-close" onclick="closeNotificationModal()" style="color: #111;">&times;</button>
                <div class="notification-overlay-label">Notificatie</div>
                <h2 id="modalTitle"></h2>
                <div class="notification-overlay-text" id="modalBody"></div>
            </div>
        </div>

        <section class="home-banner" aria-labelledby="home-banner-title">
            <p class="home-eyebrow">AED Studios</p>
            <h1 id="home-banner-title">Visuele communicatie voor bedrijven en theater</h1>
            <p class="home-banner-text">
                Plaats een banner of boodschap op de homepagina om bedrijven extra in de picture te zetten of een theater voorstelling te promoten.
            </p>
            <div class="home-banner-actions">
                <?php if (isset($_SESSION['company_id']) || (isset($_SESSION['admin']) && $_SESSION['admin'])): ?>
                    <a class="btn home-banner-edit-button btn-small" href="admin.php" style="background: #FFFFFF!important; color: #111111!important; font-weight: 700!important; border-radius: 20px!important; padding: 10px 20px!important; width: fit-content; text-decoration: none; display: flex; align-items: center; justify-content: center;">Banner aanpassen</a>
                    <a class="btn home-create-button btn-small" href="make-notification.php" style="background: rgba(255,255,255,0.1); color: #FFFFFF; font-weight: 700!important; border-radius: 20px!important; padding: 10px 20px!important; width: fit-content; text-decoration: none; display: flex; align-items: center; justify-content: center; border: 1.5px solid rgba(255,255,255,0.2);">Notificatie maken</a>
                <?php endif; ?>
            </div>
        </section>

        <div class="home-map-cta">
            <a class="btn home-map-button" href="map.php">
                <img class="home-map-button-icon" src="images/nav-ico/map.svg" alt="" aria-hidden="true">
                <span>Map</span>
            </a>
        </div>

        <div class="home-map-cta">
            <a class="btn home-map-button" href="companies.php">
                <img class="home-map-button-icon" src="../images/nav-ico/Grid.svg" alt="" aria-hidden="true">
                <span>Bedrijven</span>
            </a>
        </div>
    </main>

    <bottom-navigation></bottom-navigation>

    <script>
        function showNotificationDetails(title, text) {
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalBody').innerText = text;
            const modal = document.getElementById('notificationModal');
            modal.style.display = 'block';
            modal.classList.add('is-open');
            document.body.style.overflow = 'hidden'; // Prevent scroll
        }

        function closeNotificationModal() {
            const modal = document.getElementById('notificationModal');
            modal.style.display = 'none';
            modal.classList.remove('is-open');
            document.body.style.overflow = 'auto';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const track = document.getElementById('notificationTrack');
            const prevBtn = document.getElementById('carouselPrev');
            const nextBtn = document.getElementById('carouselNext');
            const cards = document.querySelectorAll('.notification-card');

            if (!track || cards.length <= 1) {
                if (prevBtn) prevBtn.parentElement.style.display = 'none';
                return;
            }

            let index = 0;

            const updateCarousel = () => {
                const cardWidth = cards[0].offsetWidth;
                const gap = 16;
                const offset = index * (cardWidth + gap);
                track.style.transform = `translateX(-${offset}px)`;
            };

            nextBtn.addEventListener('click', () => {
                index = (index + 1) % cards.length;
                updateCarousel();
            });

            prevBtn.addEventListener('click', () => {
                index = (index - 1 + cards.length) % cards.length;
                updateCarousel();
            });

            window.addEventListener('resize', updateCarousel);
        });
    </script>
</body>

</html>