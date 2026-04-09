<?php

declare(strict_types=1);

namespace Golpilolz\DBConfigs\Repository;

use Golpilolz\DBConfigs\Entity\SiteVariable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function save(string $name, string $value): SiteVariable
    {
        $variable = $this->findOneBy(['name' => $name]);

        if (!$variable instanceof SiteVariable) {
            $variable = new SiteVariable();
            $variable->setName($name);
        }

        $variable->setValue($value)
            ->setUpdatedAt(new \DateTime());

        $em = $this->getEntityManager();
        $em->persist($variable);
        $em->flush();

        return $variable;
    }

    public function getValue(string $name): SiteVariable
    {
        $variable = $this->findOneBy(['name' => $name]);

        if (!$variable instanceof SiteVariable) {
            $variable = new SiteVariable();
            $variable->setName($name)
                ->setValue('')
                ->setUpdatedAt(new \DateTime());

            $em = $this->getEntityManager();
            $em->persist($variable);
            $em->flush();
        }

        return $variable;
    }

    /**
     * @return array<int, SiteVariable>
     */
    #[\Override]
    public function findAll(): array
    {
        /** @var array<int, SiteVariable> $variables */
        $variables = $this->createQueryBuilder('s')
            ->addSelect('CASE WHEN s.updatedAt IS NULL THEN 1 ELSE 0 END AS HIDDEN updatedAtNull')
            ->orderBy('updatedAtNull', 'ASC')
            ->addOrderBy('s.updatedAt', 'DESC')
            ->addOrderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();

        return $variables;
    }

    public function findOneByName(string $name): ?SiteVariable
    {
        return $this->findOneBy(['name' => $name]);
    }
}
