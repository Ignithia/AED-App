<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Company;

final class CompanyRepository extends AbstractRepository
{
    /**
     * @return list<Company>
     */
    public function findAll(): array
    {
        $statement = $this->prepareAndExecute(
            'SELECT id, company_name, code, logo, bio, `spokes person` AS spokes_person, admin
             FROM company
             ORDER BY company_name ASC'
        );

        return array_map(
            static fn (array $row): Company => Company::fromRow($row),
            $statement->fetchAll(),
        );
    }

    public function findById(int $id): ?Company
    {
        $statement = $this->prepareAndExecute(
            'SELECT id, company_name, code, logo, bio, `spokes person` AS spokes_person, admin
             FROM company
             WHERE id = :id
             LIMIT 1',
            ['id' => $id]
        );

        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        /** @var array<string, mixed> $row */
        return Company::fromRow($row);
    }
}