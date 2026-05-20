<?php

declare(strict_types=1);

namespace App\Entity;

final class Picture
{
    public function __construct(
        public readonly int $id,
        public readonly string $url,
        public readonly ?int $eventId,
        public readonly ?int $notificationId = null,
    ) {}

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            url: (string) $row['url'],
            eventId: isset($row['fk_event']) ? (int) $row['fk_event'] : null,
            notificationId: isset($row['fk_notification']) ? (int) $row['fk_notification'] : null,
        );
    }
}
