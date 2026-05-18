<?php

declare(strict_types=1);

namespace App\Entity;

final class CoffeeBreakParticipant
{
    public function __construct(
        public readonly int $id,
        public readonly int $companyAccountId,
        public readonly int $coffeeBreakId,
    ) {}

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            companyAccountId: (int) $row['fk_company_account'],
            coffeeBreakId: (int) $row['fk_coffee_break'],
        );
    }
}
