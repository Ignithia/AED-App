<?php

declare(strict_types=1);

use App\Database;
use App\Repository\CompanyRepository;
use App\Repository\EmployeeRepository;
use App\Repository\TagRepository;

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Entity/Company.php';
require_once __DIR__ . '/../src/Entity/Employee.php';
require_once __DIR__ . '/../src/Entity/Tag.php';
require_once __DIR__ . '/../src/Repository/AbstractRepository.php';
require_once __DIR__ . '/../src/Repository/CompanyRepository.php';
require_once __DIR__ . '/../src/Repository/EmployeeRepository.php';
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
    $employeeRepository = new EmployeeRepository($pdo);
    $tagRepository = new TagRepository($pdo);

    $company = $companyRepository->findById($companyId);

    if ($company === null) {
        http_response_code(404);
        echo 'Company not found.';
        exit;
    }

    $employees = $employeeRepository->findByCompanyId($companyId);
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
            <h2>Employees</h2>
            <?php if ($employees === []) : ?>
                <p>No employees found.</p>
            <?php else : ?>
                <ul>
                    <?php foreach ($employees as $employee) : ?>
                        <li>
                            <?= e($employee->name) ?>
                            (<?= e($employee->code) ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>