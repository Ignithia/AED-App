<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Company;

final class CompanyRepository extends AbstractRepository
{
    /**
     * @return list<Company>
     */
    public function findAllPublic(): array
    {
        $statement = $this->prepareAndExecute(
            'SELECT id, company_name, code, logo, bio, `spokes person` AS spokes_person, admin, private, email
             FROM company
             WHERE private = 0
             ORDER BY company_name ASC'
        );

        return array_map(
            static fn(array $row): Company => Company::fromRow($row),
            $statement->fetchAll(),
        );
    }

    /**
     * @return list<Company>
     */
    public function findAll(): array
    {
        $statement = $this->prepareAndExecute(
            'SELECT id, company_name, code, logo, bio, `spokes person` AS spokes_person, admin, private, email
             FROM company
             ORDER BY company_name ASC'
        );

        return array_map(
            static fn(array $row): Company => Company::fromRow($row),
            $statement->fetchAll(),
        );
    }

    public function findById(int $id): ?Company
    {
        $statement = $this->prepareAndExecute(
            'SELECT id, company_name, code, logo, bio, `spokes person` AS spokes_person, admin, private, email
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

    /**
     * @param list<int> $tagIds
     * @return list<Company>
     */
    public function findByTags(array $tagIds): array
    {
        if (empty($tagIds)) {
            return $this->findAllPublic();
        }

        $placeholders = implode(',', array_fill(0, count($tagIds), '?'));

        $sql = "SELECT DISTINCT c.id, c.company_name, c.code, c.logo, c.bio, c.`spokes person` AS spokes_person, c.admin, c.private
                FROM company c
                INNER JOIN company_tag ct ON c.id = ct.fk_company
                WHERE ct.fk_tag IN ($placeholders)
                AND c.private = 0
                ORDER BY c.company_name ASC";

        $statement = $this->pdo->prepare($sql);
        $statement->execute($tagIds);

        return array_map(
            static fn(array $row): Company => Company::fromRow($row),
            $statement->fetchAll(),
        );
    }

    public function create(string $name, string $code, ?string $logo, ?string $bio, string $spokesPerson, bool $admin, bool $private, ?string $email = null): int
    {
        $this->prepareAndExecute(
            'INSERT INTO company (company_name, code, logo, bio, `spokes person`, admin, private, email)
             VALUES (:name, :code, :logo, :bio, :spokes_person, :admin, :private, :email)',
            [
                'name' => $name,
                'code' => $code,
                'logo' => $logo,
                'bio' => $bio,
                'spokes_person' => $spokesPerson,
                'admin' => $admin ? 1 : 0,
                'private' => $private ? 1 : 0,
                'email' => $email,
            ]
        );

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, string $name, string $code, ?string $logo, ?string $bio, string $spokesPerson, bool $admin, bool $private): void
    {
        $this->prepareAndExecute(
            'UPDATE company
             SET company_name = :name, code = :code, logo = :logo, bio = :bio, `spokes person` = :spokes_person, admin = :admin, private = :private
             WHERE id = :id',
            [
                'id' => $id,
                'name' => $name,
                'code' => $code,
                'logo' => $logo,
                'bio' => $bio,
                'spokes_person' => $spokesPerson,
                'admin' => $admin,
                'private' => $private,
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->prepareAndExecute('DELETE FROM company WHERE id = :id', ['id' => $id]);
    }
}
