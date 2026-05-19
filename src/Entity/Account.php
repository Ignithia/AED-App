<?php

declare(strict_types=1);

namespace App\Entity;

final class Account
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $code, // This is the password/code for everyone
        public readonly ?int $companyId,
        public readonly string $role,
        public readonly bool $privacySearchable,
        public readonly bool $newsletterSubscribed,
        public readonly string $language,
        public readonly bool $notificationsEnabled,
    ) {}

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            name: (string) $row['name'],
            email: (string) $row['email'],
            code: (string) $row['code'],
            companyId: $row['fk_company'] !== null ? (int) $row['fk_company'] : null,
            role: (string) ($row['role'] ?? 'guest'),
            privacySearchable: (bool) ($row['privacy_searchable'] ?? true),
            newsletterSubscribed: (bool) ($row['newsletter_subscribed'] ?? false),
            language: (string) ($row['language'] ?? 'en'),
            notificationsEnabled: (bool) ($row['notifications_enabled'] ?? true),
        );
    }
}
