<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use PDOStatement;

abstract class AbstractRepository
{
    public function __construct(protected PDO $pdo)
    {
    }

    /**
     * @param array<string, int|bool|string|null> $parameters
     */
    protected function prepareAndExecute(string $sql, array $parameters = []): PDOStatement
    {
        $statement = $this->pdo->prepare($sql);

        foreach ($parameters as $name => $value) {
            $dataType = match (true) {
                is_int($value) => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                is_null($value) => PDO::PARAM_NULL,
                default => PDO::PARAM_STR,
            };

            $statement->bindValue($name, $value, $dataType);
        }

        $statement->execute();

        return $statement;
    }
}