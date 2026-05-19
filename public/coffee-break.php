<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';

use App\Database;

session_start();

$db = Database::getInstance()->getConnection();
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

<body>
    <div class="coffee-break-page">
        <h2 class="coffee-break-page-title">Coffee break</h2>
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
                <textarea name="reason" class="coffee-break-textarea" rows="7"></textarea>
            </section>
            <div class="coffee-break-actions">
                <button class="coffee-break-primary-action" type="submit">Door gaan</button>
                <a href="index.php" class="coffee-break-secondary-action">Annuleer</a>
            </div>
        </form>
    </div>
    <bottom-navigation></bottom-navigation>
</body>

</html>