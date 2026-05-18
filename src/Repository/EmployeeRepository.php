<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Employee;

final class EmployeeRepository extends AbstractRepository
{
    /**
     * @return list<Employee>
     */
    public function findByCompanyId(int $companyId): array
    {
        $statement = $this->prepareAndExecute(
            'SELECT id, name, code, fk_company
             FROM employee
             WHERE fk_company = :company_id
             ORDER BY name ASC',
            ['company_id' => $companyId]
        );

        return array_map(
            static fn (array $row): Employee => Employee::fromRow($row),
            $statement->fetchAll(),
        );
    }
}