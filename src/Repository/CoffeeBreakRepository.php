<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CoffeeBreak;

final class CoffeeBreakRepository extends AbstractRepository
{
    /**
     * @return list<CoffeeBreak>
     */
    public function findAll(): array
    {
        $statement = $this->prepareAndExecute(
            'SELECT id, location, reason, date_time, fk_company_account
             FROM coffee_break
             ORDER BY date_time DESC'
        );

        return array_map(
            static fn(array $row): CoffeeBreak => CoffeeBreak::fromRow($row),
            $statement->fetchAll(),
        );
    }
}
