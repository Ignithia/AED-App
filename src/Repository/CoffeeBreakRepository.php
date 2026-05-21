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
        $statement = $this->prepareAndExecute(
            'SELECT cb.id, cb.location, cb.reason, cb.date_time, cb.fk_company_account, cb.status
             FROM coffee_break cb
             INNER JOIN coffee_break_participant cbp ON cbp.fk_coffee_break = cb.id
             INNER JOIN account invited_account ON invited_account.id = cbp.fk_account
             LEFT JOIN account creator_account ON creator_account.id = cb.fk_company_account
             WHERE invited_account.fk_company = :company_id_1
             AND cb.status = "pending"
             AND (creator_account.fk_company IS NULL OR creator_account.fk_company <> :company_id_2)
             ORDER BY cb.date_time ASC',
            [
                ':company_id_1' => $companyId,
                ':company_id_2' => $companyId,
            ]
        );

        return array_map(
            static fn(array $row): CoffeeBreak => CoffeeBreak::fromRow($row),
            $statement->fetchAll(),
        );
    }

    /**
     * @return list<CoffeeBreak>
     */
    public function findAcceptedByCompanyId(int $companyId): array
    {
        $statement = $this->prepareAndExecute(
            'SELECT DISTINCT cb.id, cb.location, cb.reason, cb.date_time, cb.fk_company_account, cb.status
             FROM coffee_break cb
             LEFT JOIN account creator_account ON creator_account.id = cb.fk_company_account
             LEFT JOIN coffee_break_participant cbp ON cbp.fk_coffee_break = cb.id
             LEFT JOIN account invited_account ON invited_account.id = cbp.fk_account
             WHERE cb.status = "accepted"
             AND (
                creator_account.fk_company = :company_id_1
                OR invited_account.fk_company = :company_id_2
             )
             ORDER BY cb.date_time ASC',
            [
                ':company_id_1' => $companyId,
                ':company_id_2' => $companyId,
            ]
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
            [':status' => $status, ':id' => $id]
        );
    }

    public function updateStatusForCompany(int $id, string $status, int $companyId): bool
    {
        $statement = $this->prepareAndExecute(
            'UPDATE coffee_break cb
             INNER JOIN coffee_break_participant cbp ON cbp.fk_coffee_break = cb.id
             INNER JOIN account invited_account ON invited_account.id = cbp.fk_account
             SET cb.status = :status
             WHERE cb.id = :id
             AND cb.status = "pending"
             AND invited_account.fk_company = :company_id',
            [
                ':status' => $status,
                ':id' => $id,
                ':company_id' => $companyId,
            ]
        );

        return $statement->rowCount() > 0;
    }

    public function create(string $location, string $reason, string $dateTime, int $accountId, ?int $targetCompanyId = null): int
    {
        $this->prepareAndExecute(
            'INSERT INTO coffee_break (location, reason, date_time, fk_company_account)
             VALUES (:location, :reason, :date_time, :account_id)',
            [
                ':location' => $location,
                ':reason' => $reason,
                ':date_time' => $dateTime,
                ':account_id' => $accountId,
            ]
        );

        $coffeeBreakId = (int) $this->pdo->lastInsertId();

        if ($targetCompanyId !== null) {
            $this->prepareAndExecute(
                'INSERT INTO coffee_break_participant (fk_account, fk_coffee_break)
                 SELECT a.id, :coffee_break_id
                 FROM account a
                 WHERE a.fk_company = :target_company_id
                 AND a.id <> :account_id',
                [
                    ':coffee_break_id' => $coffeeBreakId,
                    ':target_company_id' => $targetCompanyId,
                    ':account_id' => $accountId,
                ]
            );
        }

        return $coffeeBreakId;
    }
}
