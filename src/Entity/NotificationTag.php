<?php

declare(strict_types=1);

namespace App\Entity;

final class NotificationTag
{
    public function __construct(
        public readonly int $id,
        public readonly int $notificationId,
        public readonly int $tagId,
    ) {}

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            notificationId: (int) $row['fk_notification'],
            tagId: (int) $row['fk_tag'],
        );
    }
}
