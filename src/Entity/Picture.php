<?php

declare(strict_types=1);

namespace App\Entity;

final class Picture
{
    public function __construct(
        public readonly int $id,
        public readonly string $url,
        public readonly int $eventId,
    ) {}

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            url: (string) $row['url'],
            eventId: (int) $row['fk_event'],
        );
    }
}
