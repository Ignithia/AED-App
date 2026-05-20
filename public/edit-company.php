<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Repository/AbstractRepository.php';
require_once __DIR__ . '/../src/Repository/CompanyRepository.php';
require_once __DIR__ . '/../src/Entity/Company.php';

use App\Database;
use App\Repository\CompanyRepository;

session_start();

$db = Database::getInstance()->getConnection();
$companyRepo = new CompanyRepository($db);

$companyId = $_SESSION['company_id'] ?? null;
$company = $companyId ? $companyRepo->findById((int)$companyId) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $company) {
    // Basic implementation: update company name and email
    $name = $_POST['company_name'] ?? $company->companyName;
    $email = $_POST['company_email'] ?? $company->email;
    $bio = $_POST['company_description'] ?? $company->bio;
    
    // In a real app, you'd add an update method to CompanyRepository
    $stmt = $db->prepare('UPDATE company SET company_name = ?, email = ?, bio = ? WHERE id = ?');
    $stmt->execute([$name, $email, $bio, $company->id]);
    
    header('Location: profile.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AED Studios - Bewerk gegevens</title>
    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="components/bottomnavigation.js"></script>
</head>
<body class="dark-theme">

    <div class="edit-company-page">
        <h2 class="edit-company-title">Bewerk gegevens</h2>
        <div class="edit-company-line"></div>

        <?php if ($company): ?>
        <form class="edit-company-form" method="POST">
            <div class="edit-company-field">
                <label for="company-name">Bedrijfsnaam (verplicht)</label>
                <input id="company-name" name="company_name" type="text" value="<?= htmlspecialchars($company->companyName) ?>" required>
            </div>

            <div class="edit-company-field">
                <label for="company-email">Email (verplicht)</label>
                <input id="company-email" name="company_email" type="email" value="<?= htmlspecialchars($company->email) ?>" required>
            </div>

            <div class="edit-company-field">
                <label for="company-password">Wachtwoord (verplicht)</label>
                <input id="company-password" type="password" value="********" readonly>
            </div>

            <div class="edit-company-field edit-company-field-large">
                <label for="company-tags">Tags (minstens 1)</label>
                <textarea id="company-tags" rows="6" placeholder="Tags"></textarea>
            </div>

            <div class="edit-company-field">
                <label for="company-description">Beschrijving (verplicht)</label>
                <textarea id="company-description" name="company_description" rows="2" required><?= htmlspecialchars($company->bio) ?></textarea>
            </div>

            <div class="edit-company-field">
                <label for="company-contact">Contactpersoon (verplicht)</label>
                <input id="company-contact" type="text" value="<?= htmlspecialchars($company->spokesPerson) ?>" required>
            </div>

            <div class="edit-company-private-row">
                <span class="edit-company-private-label">Private:</span>
                <label class="edit-company-switch">
                    <input type="checkbox" <?= $company->admin ? 'checked' : '' ?> aria-label="Private">
                    <span class="edit-company-slider"></span>
                </label>
            </div>

            <button class="edit-company-save" type="submit">Opslaan</button>
        </form>
        <?php else: ?>
            <p>Geen bedrijf gevonden. Log eerst in.</p>
        <?php endif; ?>

        <div class="edit-company-danger">
            <div class="edit-company-danger-line"></div>
            <h3 class="edit-company-danger-title">Gevarezone</h3>
            <button class="edit-company-delete" type="button">Verwijder bedrijf</button>
        </div>
    </div>

    <bottom-navigation></bottom-navigation>

</body>
</html>