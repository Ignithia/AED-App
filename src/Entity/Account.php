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
        public readonly bool $pushNotifications,
        public readonly bool $emailNotifications,
        public readonly bool $eventPopups,
        public readonly bool $autoplayVideos,
        public readonly bool $blockPopups,
        public readonly bool $audioMuted,
        public readonly string $profileVisibility,
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
            language: (string) ($row['language'] ?? 'nl'),
            notificationsEnabled: (bool) ($row['notifications_enabled'] ?? true),
            pushNotifications: (bool) ($row['push_notifications'] ?? true),
            emailNotifications: (bool) ($row['email_notifications'] ?? true),
            eventPopups: (bool) ($row['event_popups'] ?? true),
            autoplayVideos: (bool) ($row['autoplay_videos'] ?? true),
            blockPopups: (bool) ($row['block_popups'] ?? false),
            audioMuted: (bool) ($row['audio_muted'] ?? false),
            profileVisibility: (string) ($row['profile_visibility'] ?? 'public'),
        );
    }
}
