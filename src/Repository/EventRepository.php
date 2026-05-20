<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event;

final class EventRepository extends AbstractRepository
{
    /**
     * @return list<Event>
     */
    public function findByCompanyId(int $companyId): array
    {
        $statement = $this->prepareAndExecute(
            'SELECT e.*
             FROM event e
             JOIN event_participant ep ON e.id = ep.fk_event
             JOIN account a ON ep.fk_account = a.id
             WHERE a.fk_company = :company_id
             ORDER BY e.start_time ASC',
            ['company_id' => $companyId]
        );

        return array_map(
            static fn(array $row): Event => Event::fromRow($row),
            $statement->fetchAll(),
        );
    }

    /**
     * @return list<Event>
     */
    public function findAll(): array
    {
        $statement = $this->prepareAndExecute(
            'SELECT *
             FROM event
             ORDER BY start_time ASC'
        );

        return array_map(
            static fn(array $row): Event => Event::fromRow($row),
            $statement->fetchAll(),
        );
    }

    /**
     * @return list<Event>
     */
    public function findAllPublic(): array
    {
        $statement = $this->prepareAndExecute(
            'SELECT *
             FROM event
             WHERE fk_company IS NULL
             ORDER BY start_time ASC'
        );

        return array_map(
            static fn(array $row): Event => Event::fromRow($row),
            $statement->fetchAll(),
        );
    }

    /**
     * @param list<int> $tagIds
     * @return list<Event>
     */
    public function findByTags(array $tagIds): array
    {
        if (empty($tagIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($tagIds), '?'));

        $sql = "SELECT DISTINCT e.*
                FROM event e
                INNER JOIN event_tag et ON e.id = et.fk_event
                WHERE et.fk_tag IN ($placeholders)
                ORDER BY e.start_time ASC";

        $statement = $this->pdo->prepare($sql);
        $statement->execute($tagIds);

        return array_map(
            static fn(array $row): Event => Event::fromRow($row),
            $statement->fetchAll(),
        );
    }

    public function findById(int $id): ?Event
    {
        $statement = $this->prepareAndExecute(
            'SELECT e.*, 
                    c.company_name, c.`spokes person` as spokes_person, c.Telefoon as company_phone
             FROM event e
             LEFT JOIN company c ON e.fk_company = c.id
             WHERE e.id = :id',
            ['id' => $id]
        );

        $row = $statement->fetch();

        return $row ? Event::fromRow($row) : null;
    }

    public function create(string $name, string $info, string $startTime, string $endTime, ?int $companyId): int
    {
        $this->prepareAndExecute(
            'INSERT INTO event (event_name, event_info, start_time, end_time, fk_company)
             VALUES (:name, :info, :start, :end, :company_id)',
            [
                'name' => $name,
                'info' => $info,
                'start' => $startTime,
                'end' => $endTime,
                'company_id' => $companyId,
            ]
        );

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, string $name, string $info, string $startTime, string $endTime, ?int $companyId): void
    {
        $this->prepareAndExecute(
            'UPDATE event
             SET event_name = :name, event_info = :info, start_time = :start, end_time = :end, fk_company = :company_id
             WHERE id = :id',
            [
                'id' => $id,
                'name' => $name,
                'info' => $info,
                'start' => $startTime,
                'end' => $endTime,
                'company_id' => $companyId,
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->prepareAndExecute('DELETE FROM event WHERE id = :id', ['id' => $id]);
    }

    public function addParticipant(int $eventId, int $accountId): void
    {
        $this->prepareAndExecute(
            'INSERT IGNORE INTO event_participant (fk_event, fk_account) VALUES (:event_id, :account_id)',
            [
                'event_id' => $eventId,
                'account_id' => $accountId,
            ]
        );
    }
}
