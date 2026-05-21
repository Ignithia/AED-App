<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Notification;

final class NotificationRepository extends AbstractRepository
{
    /**
     * @return list<Notification>
     */
    public function findAllActive(): array
    {
        $statement = $this->prepareAndExecute(
            'SELECT id, title, body_text, image, expiry_date, fk_employee
             FROM notification
             WHERE expiry_date > NOW() OR expiry_date IS NULL
             ORDER BY id DESC'
        );

        return array_map(
            static fn(array $row): Notification => Notification::fromRow($row),
            $statement->fetchAll(),
        );
    }

    /**
     * @return list<Notification>
     */
    public function findActiveByEmployeeId(int $employeeId): array
    {
        $statement = $this->prepareAndExecute(
            'SELECT id, title, body_text, image, expiry_date, fk_employee
             FROM notification
             WHERE fk_employee = :employee_id
             AND expiry_date > NOW()
             ORDER BY expiry_date ASC',
            [':employee_id' => $employeeId]
        );

        return array_map(
            static fn(array $row): Notification => Notification::fromRow($row),
            $statement->fetchAll(),
        );
    }

    /**
     * @param list<int> $tagIds
     * @return list<Notification>
     */
    public function findByTags(array $tagIds): array
    {
        if (empty($tagIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($tagIds), '?'));

        $sql = "SELECT DISTINCT n.id, n.title, n.body_text, n.image, n.expiry_date, n.fk_employee
                FROM notification n
                INNER JOIN notification_tag nt ON n.id = nt.fk_notification
                WHERE nt.fk_tag IN ($placeholders)
                AND (n.expiry_date IS NULL OR n.expiry_date > NOW())
                ORDER BY n.id DESC";

        $statement = $this->pdo->prepare($sql);
        $statement->execute($tagIds);

        return array_map(
            static fn(array $row): Notification => Notification::fromRow($row),
            $statement->fetchAll(),
        );
    }

    public function findById(int $id): ?Notification
    {
        $statement = $this->prepareAndExecute(
            'SELECT id, title, body_text, image, expiry_date, fk_employee
             FROM notification
             WHERE id = :id',
            [':id' => $id]
        );

        $row = $statement->fetch();

        return $row ? Notification::fromRow($row) : null;
    }

    public function create(string $title, string $body, ?string $image, ?string $expiryDate, ?int $accountId): int
    {
        $this->prepareAndExecute(
            'INSERT INTO notification (title, body_text, image, expiry_date, fk_employee)
             VALUES (:title, :body, :image, :expiry, :account_id)',
            [
                ':title' => $title,
                ':body' => $body,
                ':image' => $image,
                ':expiry' => $expiryDate,
                ':account_id' => $accountId,
            ]
        );

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, string $title, string $body, ?string $image, ?string $expiryDate, ?int $accountId): void
    {
        $this->prepareAndExecute(
            'UPDATE notification
             SET title = :title, body_text = :body, image = :image, expiry_date = :expiry, fk_employee = :account_id
             WHERE id = :id',
            [
                ':id' => $id,
                ':title' => $title,
                ':body' => $body,
                ':image' => $image,
                ':expiry' => $expiryDate,
                ':account_id' => $accountId,
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->prepareAndExecute('DELETE FROM notification WHERE id = :id', [':id' => $id]);
    }
}
