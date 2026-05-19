<?php

declare(strict_types=1);

namespace App\Entity;

final class CoffeeBreak
{
    public function __construct(
        public readonly int $id,
        public readonly string $location,
        public readonly string $reason,
        public readonly ?string $dateTime,
        public readonly ?int $companyAccountId,
    ) {}

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            location: (string) $row['location'],
            reason: (string) $row['reason'],
            dateTime: $row['date_time'] ? (string) $row['date_time'] : null,
            companyAccountId: $row['fk_company_account'] ? (int) $row['fk_company_account'] : null,
        );
    }
}
