<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Entity/Company.php';
require_once __DIR__ . '/../src/Entity/Account.php';
require_once __DIR__ . '/../src/Entity/Tag.php';
require_once __DIR__ . '/../src/Repository/AbstractRepository.php';
require_once __DIR__ . '/../src/Repository/CompanyRepository.php';
require_once __DIR__ . '/../src/Repository/AccountRepository.php';
require_once __DIR__ . '/../src/Repository/TagRepository.php';

use App\Database;
use App\Repository\CompanyRepository;
use App\Repository\AccountRepository;
use App\Repository\TagRepository;

session_start();

/**
 * Escape all dynamic HTML output to prevent XSS.
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$companyId = filter_input(INPUT_GET, 'company_id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);

if ($companyId === null || $companyId === false) {
    http_response_code(400);
    echo 'Invalid company id.';
    exit;
}

try {
    $pdo = Database::getInstance()->getConnection();
    $companyRepository = new CompanyRepository($pdo);
    $accountRepository = new AccountRepository($pdo);
    $tagRepository = new TagRepository($pdo);

    $company = $companyRepository->findById($companyId);

    if ($company === null) {
        http_response_code(404);
        echo 'Company not found.';
        exit;
    }

    $accounts = $accountRepository->findByCompanyId($companyId);
    $tags = $tagRepository->findByCompanyId($companyId);
} catch (Throwable $exception) {
    // Never expose raw database details to the browser.
    error_log($exception->getMessage());
    http_response_code(500);
    echo 'A database error occurred. Please try again later.';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($company->companyName) ?></title>
    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="components/bottomnavigation.js"></script>
</head>

<body class="dark-theme">
    <main class="company-detail-page">
        <section class="company-detail-logo-wrap">
            <img src="<?= e($company->logo) ?>" alt="<?= e($company->companyName) ?> logo" class="company-detail-logo">
        </section>

        <h1 class="company-detail-title"><?= e($company->companyName) ?></h1>
        <div class="company-detail-line"></div>

        <section class="company-detail-section">
            <div class="company-detail-info-block">
                <div class="company-detail-row">
                    <span>Telefoon:</span>
                    <span><?= e($company->code) ?></span>
                </div>
                <div class="company-detail-row">
                    <span>Contact:</span>
                    <span><?= e($company->spokesPerson) ?></span>
                </div>
                <div class="company-detail-row">
                    <span>Email:</span>
                    <span><?= e($company->email ?? '') ?></span>
                </div>
            </div>
        </section>

        <section class="company-detail-section">
            <h3 class="company-detail-section-title">Intro:</h3>
            <p class="company-detail-text"><?= $company->bio ? nl2br(e($company->bio)) : 'Geen introductie beschikbaar.' ?></p>
        </section>

        <section class="company-detail-section">
            <h3 class="company-detail-section-title">Tags:</h3>
            <div class="company-detail-tags-grid">
                <?php foreach ($tags as $index => $tag) : ?>
                    <div class="company-detail-tag-card <?= ($index === count($tags) - 1 && count($tags) % 2 !== 0) ? 'company-detail-tag-card-wide' : '' ?>">
                        <div class="company-detail-tag-hole"></div>
                        <span class="company-detail-tag-label"><?= e($tag->name) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="company-detail-actions">
            <div class="company-detail-primary-wrap">
                <a href="coffee-break.php?company_id=<?= $company->id ?>" class="company-detail-primary">Coffee break</a>
                <button class="company-detail-info-dot" onclick="alert('Indien u hier op klikt stuurt u een seintje dat u graag een koffie of drankje komt drinken bij dit bedrijf.')">i</button>
            </div>
            <a href="mailto:<?= e($company->email ?? '') ?>" class="company-detail-secondary">Contact</a>
        </div>
    </main>

    <bottom-navigation></bottom-navigation>
</body>

</html>