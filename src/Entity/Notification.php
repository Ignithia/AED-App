<?php

declare(strict_types=1);

namespace App\Entity;

final class Notification
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $bodyText,
        public readonly ?string $image,
        public readonly string $expiryDate,
        public readonly int $employeeId,
    ) {}

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            title: (string) $row['title'],
            bodyText: (string) $row['body_text'],
            image: $row['image'] ? (string) $row['image'] : null,
            expiryDate: (string) $row['expiry_date'],
            employeeId: (int) $row['fk_employee'],
        );
    }
}
