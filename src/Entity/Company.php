<?php

declare(strict_types=1);

namespace App\Entity;

final class Company
{
    public function __construct(
        public readonly int $id,
        public readonly string $companyName,
        public readonly string $code,
        public readonly string $logo,
        public readonly string $bio,
        public readonly string $spokesPerson,
        public readonly bool $admin,
        public readonly bool $private,
        public readonly ?string $email = null,
    ) {}

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            companyName: (string) $row['company_name'],
            code: (string) $row['code'],
            logo: (string) $row['logo'],
            bio: (string) $row['bio'],
            spokesPerson: (string) $row['spokes_person'],
            private: (bool) ($row['private'] ?? false),
            admin: (bool) ($row['admin'] ?? false),
            email: (string) ($row['email'] ?? ''),
        );
    }
}
