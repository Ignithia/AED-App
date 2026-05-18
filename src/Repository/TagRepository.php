<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tag;

final class TagRepository extends AbstractRepository
{
    /**
     * @return list<Tag>
     */
    public function findByCompanyId(int $companyId): array
    {
        $statement = $this->prepareAndExecute(
            'SELECT t.id, t.name
             FROM company_tag ct
             INNER JOIN tag t ON t.id = ct.fk_tag
             WHERE ct.fk_company = :company_id
             ORDER BY t.name ASC',
            ['company_id' => $companyId]
        );

        return array_map(
            static fn (array $row): Tag => Tag::fromRow($row),
            $statement->fetchAll(),
        );
    }
}