<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Account;

final class AccountRepository extends AbstractRepository
{
    /**
     * @return list<Account>
     */
    public function findByCompanyId(int $companyId): array
    {
        $statement = $this->prepareAndExecute(
            'SELECT *
             FROM account
             WHERE fk_company = :company_id
             ORDER BY name ASC',
            ['company_id' => $companyId]
        );

        return array_map(
            static fn(array $row): Account => Account::fromRow($row),
            $statement->fetchAll(),
        );
    }

    public function findByEmail(string $email): ?Account
    {
        $statement = $this->prepareAndExecute(
            'SELECT *
             FROM account
             WHERE email = :email
             LIMIT 1',
            ['email' => $email]
        );

        $row = $statement->fetch();

        return $row ? Account::fromRow($row) : null;
    }

    /**
     * Authenticate an account using email and code
     */
    public function findByEmailAndCode(string $email, string $code): ?Account
    {
        $statement = $this->prepareAndExecute(
            'SELECT *
             FROM account
             WHERE email = :email AND code = :code
             LIMIT 1',
            ['email' => $email, 'code' => $code]
        );

        $row = $statement->fetch();

        return $row ? Account::fromRow($row) : null;
    }

    /**
     * Find an account by its unique code (password)
     */
    public function findByCode(string $code): ?Account
    {
        $statement = $this->prepareAndExecute(
            'SELECT *
             FROM account
             WHERE code = :code
             LIMIT 1',
            ['code' => $code]
        );

        $row = $statement->fetch();

        return $row ? Account::fromRow($row) : null;
    }

    public function findById(int $id): ?Account
    {
        $statement = $this->prepareAndExecute(
            'SELECT *
             FROM account
             WHERE id = :id
             LIMIT 1',
            ['id' => $id]
        );

        $row = $statement->fetch();

        return $row ? Account::fromRow($row) : null;
    }

    /**
     * Create multiple empty accounts for a company with customizable code and prefix
     */
    public function bulkCreateForCompany(int $companyId, int $count, ?string $codeOverride = null, string $namePrefix = 'Medewerker'): void
    {
        $sql = 'INSERT INTO account (name, email, code, fk_company) VALUES (:name, :email, :code, :company_id)';
        $stmt = $this->pdo->prepare($sql);

        for ($i = 1; $i <= $count; $i++) {
            $uniqueId = bin2hex(random_bytes(3));
            $loginCode = $codeOverride ?: bin2hex(random_bytes(4));
            $stmt->execute([
                'name' => "$namePrefix $i",
                'email' => "pending_{$uniqueId}@aedstudios.com", // Generic internal email
                'code' => $loginCode,
                'company_id' => $companyId
            ]);
        }
    }

    /**
     * Create a guest account with email and a chosen code (password)
     */
    public function createGuest(string $email, string $code, string $name): void
    {
        $this->prepareAndExecute(
            'INSERT INTO account (name, email, code, fk_company) 
             VALUES (:name, :email, :code, NULL)',
            [
                'name' => $name,
                'email' => $email,
                'code' => $code
            ]
        );
    }

    /**
     * Get or create a guest account by email
     */
    public function getOrCreateGuestByEmail(string $email): Account
    {
        $account = $this->findByEmail($email);
        if ($account) {
            return $account;
        }

        $code = bin2hex(random_bytes(4));
        $name = explode('@', $email)[0];

        $this->prepareAndExecute(
            'INSERT INTO account (name, email, code, fk_company, role) 
             VALUES (:name, :email, :code, NULL, "guest")',
            [
                'name' => $name,
                'email' => $email,
                'code' => $code
            ]
        );

        return $this->findByEmail($email);
    }

    public function update(int $id, string $name, string $email, string $code, ?int $companyId): void
    {
        $this->prepareAndExecute(
            'UPDATE account
             SET name = :name, email = :email, code = :code, fk_company = :company_id
             WHERE id = :id',
            [
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'code' => $code,
                'company_id' => $companyId,
            ]
        );
    }

    public function updateSettings(
        int $accountId,
        bool $privacy,
        bool $newsletter,
        string $language,
        bool $notifications
    ): void {
        $this->prepareAndExecute(
            'UPDATE account 
             SET privacy_searchable = :privacy, 
                 newsletter_subscribed = :newsletter, 
                 language = :lang, 
                 notifications_enabled = :notif 
             WHERE id = :id',
            [
                'privacy' => $privacy,
                'newsletter' => $newsletter,
                'lang' => $language,
                'notif' => $notifications,
                'id' => $accountId
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->prepareAndExecute('DELETE FROM account WHERE id = :id', ['id' => $id]);
    }
}
