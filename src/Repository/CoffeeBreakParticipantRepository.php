<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CoffeeBreakParticipant;

final class CoffeeBreakParticipantRepository extends AbstractRepository
{
    /**
     * @return list<CoffeeBreakParticipant>
     */
    public function findByCoffeeBreakId(int $coffeeBreakId): array
    {
        $statement = $this->prepareAndExecute(
            'SELECT id, fk_company_account, fk_coffee_break
             FROM coffee_break_participant
             WHERE fk_coffee_break = :coffee_break_id',
            [':coffee_break_id' => $coffeeBreakId]
        );

        return array_map(
            static fn(array $row): CoffeeBreakParticipant => CoffeeBreakParticipant::fromRow($row),
            $statement->fetchAll(),
        );
    }
}
