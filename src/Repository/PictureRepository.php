<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Picture;

final class PictureRepository extends AbstractRepository
{
    /**
     * @return list<Picture>
     */
    public function findByEventId(int $eventId): array
    {
        $statement = $this->prepareAndExecute(
            'SELECT *
             FROM picture
             WHERE fk_event = :event_id',
            ['event_id' => $eventId]
        );

        return array_map(
            static fn(array $row): Picture => Picture::fromRow($row),
            $statement->fetchAll(),
        );
    }

    public function create(string $url, int $eventId): void
    {
        $this->prepareAndExecute(
            'INSERT INTO picture (url, fk_event)
             VALUES (:url, :event_id)',
            [
                'url' => $url,
                'event_id' => $eventId,
            ]
        );
    }
}
