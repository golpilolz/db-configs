<?php

declare(strict_types=1);

namespace Golpilolz\DBConfigs\Tests\Repository;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;
use Golpilolz\DBConfigs\Entity\SiteVariable;
use Golpilolz\DBConfigs\Repository\SiteVariableRepository;
use PHPUnit\Framework\TestCase;

/**
 * Runs against an in-memory SQLite database: the repository writes with raw
 * DBAL queries, which mocks would not check.
 */
class SiteVariableRepositoryTest extends TestCase
{
    private EntityManager $em;
    private SiteVariableRepository $repository;

    protected function setUp(): void
    {
        $config = ORMSetup::createAttributeMetadataConfig([\dirname(__DIR__, 2) . '/Entity'], true);
        if (\PHP_VERSION_ID >= 80400) {
            $config->enableNativeLazyObjects(true);
        } else {
            $config->setProxyDir(sys_get_temp_dir());
            $config->setProxyNamespace('DBConfigsTestProxies');
        }

        $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true], $config);
        $this->em = new EntityManager($connection, $config);
        (new SchemaTool($this->em))->createSchema($this->em->getMetadataFactory()->getAllMetadata());

        $registry = $this->createStub(ManagerRegistry::class);
        $registry->method('getManagerForClass')->willReturn($this->em);
        $this->repository = new SiteVariableRepository($registry);
    }

    public function testSaveInsertsNewVariable(): void
    {
        $this->repository->save('new_key', 'new_value');

        $variable = $this->repository->findOneByName('new_key');
        self::assertNotNull($variable);
        self::assertSame('new_value', $variable->getValue());
        self::assertNotNull($variable->getUpdatedAt());
    }

    public function testSaveUpdatesExistingVariableWithoutDuplicate(): void
    {
        $this->repository->save('key', 'old');
        $this->repository->save('key', 'new');

        self::assertCount(1, $this->repository->findAll());
        self::assertSame('new', $this->repository->findOneByName('key')?->getValue());
    }

    public function testSaveRefreshesAlreadyLoadedEntity(): void
    {
        $this->repository->save('key', 'old');
        $loaded = $this->repository->findOneByName('key');

        $this->repository->save('key', 'new');

        self::assertSame('new', $loaded?->getValue());
    }

    public function testSaveDoesNotFlushOtherPendingChanges(): void
    {
        $this->repository->save('other', 'untouched');
        $this->repository->findOneByName('other')?->setValue('pending, not confirmed');

        $this->repository->save('key', 'value');
        $this->em->clear();

        self::assertSame('untouched', $this->repository->findOneByName('other')?->getValue());
    }

    public function testFindOneByNameReturnsNullWhenMissing(): void
    {
        self::assertNull($this->repository->findOneByName('missing'));
        self::assertSame([], $this->repository->findAll());
    }

    public function testFindAllOrdersByMostRecentThenName(): void
    {
        $metadata = $this->em->getClassMetadata(SiteVariable::class);

        foreach (['b' => '2024-01-01', 'a' => '2024-01-01', 'c' => '2025-01-01'] as $name => $date) {
            $this->em->persist((new SiteVariable())->setName($name)->setValue('v'));
            $this->em->flush();
            $this->em->getConnection()->update(
                $metadata->getTableName(),
                [$metadata->getColumnName('updatedAt') => $date . ' 00:00:00'],
                [$metadata->getColumnName('name') => $name],
            );
        }
        $this->em->clear();

        $names = array_map(static fn (SiteVariable $v) => $v->getName(), $this->repository->findAll());

        self::assertSame(['c', 'a', 'b'], $names);
    }
}
