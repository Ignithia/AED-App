<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tag;

final class NotificationTagRepository extends AbstractRepository
{
    /**
     * @return list<Tag>
     */
    public function findTagsByNotificationId(int $notificationId): array
    {
        $statement = $this->prepareAndExecute(
            'SELECT t.id, t.name
             FROM notification_tag nt
             INNER JOIN tag t ON t.id = nt.fk_tag
             WHERE nt.fk_notification = :notification_id
             ORDER BY t.name ASC',
            ['notification_id' => $notificationId]
        );

        return array_map(
            static fn(array $row): Tag => Tag::fromRow($row),
            $statement->fetchAll(),
        );
    }

    /**
     * Add a tag to a notification
     */
    public function addTagToNotification(int $notificationId, int $tagId): void
    {
        $this->prepareAndExecute(
            'INSERT INTO notification_tag (fk_notification, fk_tag) VALUES (:notification_id, :tag_id)',
            [
                'notification_id' => $notificationId,
                'tag_id' => $tagId
            ]
        );
    }
}
