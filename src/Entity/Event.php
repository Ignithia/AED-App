<?php

declare(strict_types=1);

namespace App\Entity;

final class Event
{
    public readonly \DateTimeImmutable $startTime;
    public readonly \DateTimeImmutable $endTime;

    public function __construct(
        public readonly int $id,
        public readonly string $eventName,
        public readonly string $eventInfo,
        string $startTime,
        string $endTime,
        public readonly ?int $companyId,
        public readonly ?string $companyName = null,
        public readonly ?string $spokesPerson = null,
        public readonly ?string $companyPhone = null,
    ) {
        try {
            $this->startTime = new \DateTimeImmutable($startTime);
        } catch (\Exception) {
            $this->startTime = new \DateTimeImmutable('1970-01-01 00:00:00');
        }
        
        try {
            $this->endTime = new \DateTimeImmutable($endTime);
        } catch (\Exception) {
            $this->endTime = new \DateTimeImmutable('1970-01-01 00:00:00');
        }
    }

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
            companyId: $row['fk_company'] ? (int) $row['fk_company'] : null,
            companyName: $row['company_name'] ?? null,
            spokesPerson: $row['spokes_person'] ?? null,
            companyPhone: $row['company_phone'] ?? null,
        );
    }
}
