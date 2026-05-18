<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Notification;

final class NotificationRepository extends AbstractRepository
{
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
            ['employee_id' => $employeeId]
        );

        return array_map(
            static fn(array $row): Notification => Notification::fromRow($row),
            $statement->fetchAll(),
        );
    }
}
