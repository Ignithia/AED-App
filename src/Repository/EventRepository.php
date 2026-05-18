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
            'SELECT id, event_name, event_info, start_time, end_time, fk_company
             FROM event
             WHERE fk_company = :company_id
             ORDER BY start_time ASC',
            ['company_id' => $companyId]
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

        $sql = "SELECT DISTINCT e.id, e.event_name, e.event_info, e.start_time, e.end_time, e.fk_company
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
            'SELECT id, event_name, event_info, start_time, end_time, fk_company
             FROM event
             WHERE id = :id',
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
}
