<?php

declare(strict_types=1);

use App\Database;
use App\Repository\CompanyRepository;
use App\Repository\AccountRepository;
use App\Repository\TagRepository;

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Entity/Company.php';
require_once __DIR__ . '/../src/Entity/Account.php';
require_once __DIR__ . '/../src/Entity/Tag.php';
require_once __DIR__ . '/../src/Repository/AbstractRepository.php';
require_once __DIR__ . '/../src/Repository/CompanyRepository.php';
require_once __DIR__ . '/../src/Repository/AccountRepository.php';
require_once __DIR__ . '/../src/Repository/TagRepository.php';

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
    // Keep credentials out of source code; environment variables are preferred.
    $database = new Database(
        host: $_ENV['DB_HOST'] ?? '127.0.0.1',
        dbName: $_ENV['DB_NAME'] ?? 'aed_db',
        username: $_ENV['DB_USER'] ?? 'root',
        password: $_ENV['DB_PASS'] ?? '',
        port: (int) ($_ENV['DB_PORT'] ?? 3306),
    );

    $pdo = $database->getConnection();
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
</head>

<body>
    <main>
        <h1><?= e($company->companyName) ?></h1>
        <p><strong>Code:</strong> <?= e($company->code) ?></p>
        <p><strong>Spokes person:</strong> <?= e($company->spokesPerson) ?></p>
        <p><strong>Admin:</strong> <?= $company->admin ? 'Yes' : 'No' ?></p>

        <section>
            <h2>Logo</h2>
            <img src="<?= e($company->logo) ?>" alt="<?= e($company->companyName) ?> logo">
        </section>

        <section>
            <h2>Bio</h2>
            <p><?= nl2br(e($company->bio)) ?></p>
        </section>

        <section>
            <h2>Tags</h2>
            <?php if ($tags === []) : ?>
                <p>No tags found.</p>
            <?php else : ?>
                <ul>
                    <?php foreach ($tags as $tag) : ?>
                        <li><?= e($tag->name) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <section>
            <h2>Accounts</h2>
            <?php if ($accounts === []) : ?>
                <p>No accounts found.</p>
            <?php else : ?>
                <ul>
                    <?php foreach ($accounts as $account) : ?>
                        <li>
                            <?= e($account->name) ?>
                            (<?= e($account->email) ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </main>
</body>

</html>