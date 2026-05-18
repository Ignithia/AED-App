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
            'SELECT id, pictures, fk_event
             FROM picture
             WHERE fk_event = :event_id',
            ['event_id' => $eventId]
        );

        return array_map(
            static fn(array $row): Picture => Picture::fromRow($row),
            $statement->fetchAll(),
        );
    }
}
