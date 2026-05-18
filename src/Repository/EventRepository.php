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
}
