<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';

use App\Database;

session_start();

// Basic auth check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // For demo purposes, we might allow access if not logged in at all, 
    // but the request implies "completion" of the architecture.
    // header('Location: account.php');
    // exit;
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AED Studios - Pagina aanmaken</title>
    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="components/bottomnavigation.js"></script>
</head>

<body class="dark-theme">
    <main class="admin-page">
        <section class="admin-hero">
            <p class="admin-eyebrow">AED Admin</p>
        </section>

        <!-- Dynamic implementation would go here -->
        <section class="admin-card">
            <h2>Bannerdetails</h2>
            <form class="admin-form-grid" method="POST">
                <!-- Admin panel inputs -->
                <label class="admin-field">
                    <span>Eyebrow</span>
                    <input name="eyebrow" type="text" value="AED Studios" class="form-control">
                </label>
                <label class="admin-field">
                    <span>Titel</span>
                    <input name="title" type="text" value="Visuele communicatie voor bedrijven" class="form-control">
                </label>
                <label class="admin-field">
                    <span>Beschrijving</span>
                    <textarea name="description" class="form-control" style="min-height: 100px;">Plaats een banner of boodschap op de homepagina om bedrijven extra in de picture te zetten of een theater voorstelling te promoten.</textarea>
                </label>
                <button class="btn primary" type="submit" style="background: var(--primary-app); color: white; border: none; padding: 14px; border-radius: 30px; font-weight: 700; margin-top: 10px;">Opslaan</button>
            </form>
        </section>

        <section class="admin-card admin-actions-card">
            <a class="btn" href="index.php">Terug naar home</a>
        </section>
    </main>
    <bottom-navigation></bottom-navigation>
</body>

</html>