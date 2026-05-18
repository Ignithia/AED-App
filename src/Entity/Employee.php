<?php

declare(strict_types=1);

namespace App\Entity;

final class Employee
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $code,
        public readonly int $companyId,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            name: (string) $row['name'],
            code: (string) $row['code'],
            companyId: (int) $row['fk_company'],
        );
    }
}