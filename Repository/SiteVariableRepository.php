<?php

declare(strict_types=1);

namespace Golpilolz\DBConfigs\Repository;

use Golpilolz\DBConfigs\Entity\SiteVariable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SiteVariable>
 */
class SiteVariableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SiteVariable::class);
    }

    /**
     * Upsert written with DBAL, not the ORM: it does not flush the caller's
     * other pending changes, and two concurrent saves of a new name cannot
     * crash on the primary key.
     */
    public function save(string $name, string $value): void
    {
        $em = $this->getEntityManager();
        $metadata = $this->getClassMetadata();
        $connection = $em->getConnection();

        $table = $metadata->getTableName();
        $nameColumn = $metadata->getColumnName('name');
        $data = [
            $metadata->getColumnName('value') => $value,
            $metadata->getColumnName('updatedAt') => new \DateTime(),
        ];
        $types = [$metadata->getColumnName('updatedAt') => Types::DATETIME_MUTABLE];

        if ($connection->update($table, $data, [$nameColumn => $name], $types) === 0) {
            try {
                $connection->insert($table, [$nameColumn => $name] + $data, $types);
            } catch (UniqueConstraintViolationException) {
                // Another process inserted the same name in between: ours is the latest value.
                $connection->update($table, $data, [$nameColumn => $name], $types);
            }
        }

        // Keep an entity already loaded in this request in sync with the database.
        $managed = $em->getUnitOfWork()->tryGetById($name, SiteVariable::class);
        if ($managed !== false) {
            $em->refresh($managed);
        }
    }

    /**
     * @return array<int, SiteVariable>
     */
    #[\Override]
    public function findAll(): array
    {
        return $this->findBy([], ['updatedAt' => 'DESC', 'name' => 'ASC']);
    }

    public function findOneByName(string $name): ?SiteVariable
    {
        return $this->findOneBy(['name' => $name]);
    }
}
