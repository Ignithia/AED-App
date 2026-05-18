<?php

declare(strict_types=1);

namespace App\Entity;

final class Event
{
    public function __construct(
        public readonly int $id,
        public readonly string $eventName,
        public readonly string $eventInfo,
        public readonly string $startTime,
        public readonly string $endTime,
        public readonly int $companyId,
    ) {}

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            eventName: (string) $row['event_name'],
            eventInfo: (string) $row['event_info'],
            startTime: (string) $row['start_time'],
            endTime: (string) $row['end_time'],
            companyId: (int) $row['fk_company'],
        );
    }
}
