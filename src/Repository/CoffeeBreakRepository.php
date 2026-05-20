<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CoffeeBreak;

final class CoffeeBreakRepository extends AbstractRepository
{
    /**
     * @return list<CoffeeBreak>
     */
    public function findAll(): array
    {
        $statement = $this->prepareAndExecute(
            'SELECT id, location, reason, date_time, fk_company_account, status
             FROM coffee_break
             ORDER BY date_time DESC'
        );

        return array_map(
            static fn(array $row): CoffeeBreak => CoffeeBreak::fromRow($row),
            $statement->fetchAll(),
        );
    }

    /**
     * @return list<CoffeeBreak>
     */
    public function findPendingByCompanyId(int $companyId): array
    {
        // Finds coffee breaks related to this company or account
        $statement = $this->prepareAndExecute(
            'SELECT cb.id, cb.location, cb.reason, cb.date_time, cb.fk_company_account, cb.status
             FROM coffee_break cb
             LEFT JOIN account a ON cb.fk_company_account = a.id
             WHERE a.fk_company = :company_id AND cb.status = "pending"
             ORDER BY cb.date_time ASC',
            ['company_id' => $companyId]
        );

        return array_map(
            static fn(array $row): CoffeeBreak => CoffeeBreak::fromRow($row),
            $statement->fetchAll(),
        );
    }

    public function updateStatus(int $id, string $status): void
    {
        $this->prepareAndExecute(
            'UPDATE coffee_break SET status = :status WHERE id = :id',
            ['status' => $status, 'id' => $id]
        );
    }

    public function create(string $location, string $reason, string $dateTime, int $accountId): int
    {
        $this->prepareAndExecute(
            'INSERT INTO coffee_break (location, reason, date_time, fk_company_account)
             VALUES (:location, :reason, :date_time, :account_id)',
            [
                'location' => $location,
                'reason' => $reason,
                'date_time' => $dateTime,
                'account_id' => $accountId,
            ]
        );

        return (int) $this->pdo->lastInsertId();
    }
}
