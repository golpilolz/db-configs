<?php

declare(strict_types=1);

namespace Golpilolz\DBConfigs\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Golpilolz\DBConfigs\Entity\SiteVariable;
use Golpilolz\DBConfigs\Repository\SiteVariableRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SiteVariableRepositoryTest extends TestCase
{
    /** @var SiteVariableRepository&MockObject */
    private SiteVariableRepository $repository;

    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // Partial mock: we mock the inherited Doctrine methods so we can test
        // only the custom logic without a real database connection.
        $this->repository = $this->getMockBuilder(SiteVariableRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOneBy', 'getEntityManager', 'createQueryBuilder', 'findOneByName'])
            ->getMock();

        $this->repository
            ->method('getEntityManager')
            ->willReturn($this->entityManager);
    }

    public function testSaveCreatesNewVariableWhenNotFound(): void
    {
        $this->repository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['name' => 'new_key'])
            ->willReturn(null);

        $this->entityManager->expects(self::once())->method('persist');
        $this->entityManager->expects(self::once())->method('flush');

        $result = $this->repository->save('new_key', 'new_value');

        self::assertSame('new_key', $result->getName());
        self::assertSame('new_value', $result->getValue());
    }
//
//    public function testSaveUpdatesExistingVariable(): void
//    {
//        $existing = (new SiteVariable())->setName('existing_key')->setValue('old_value');
//
//        $this->repository
//            ->expects(self::once())
//            ->method('findOneBy')
//            ->with(['name' => 'existing_key'])
//            ->willReturn($existing);
//
//        $this->entityManager->expects(self::once())->method('persist');
//        $this->entityManager->expects(self::once())->method('flush');
//
//        $result = $this->repository->save('existing_key', 'new_value');
//
//        self::assertSame('existing_key', $result->getName());
//        self::assertSame('new_value', $result->getValue());
//        self::assertSame($existing, $result);
//    }
//
//    public function testSaveReturnsSiteVariableInstance(): void
//    {
//        $this->repository->method('findOneBy')->willReturn(null);
//        $this->entityManager->method('persist');
//        $this->entityManager->method('flush');
//
//        $result = $this->repository->save('k', 'v');
//
//        self::assertInstanceOf(SiteVariable::class, $result);
//    }
//
//    public function testSaveSetsUpdatedAt(): void
//    {
//        $this->repository->method('findOneBy')->willReturn(null);
//        $this->entityManager->method('persist');
//        $this->entityManager->method('flush');
//
//        $before = new \DateTime();
//        $result = $this->repository->save('k', 'v');
//        $after = new \DateTime();
//
//        self::assertNotNull($result->getUpdatedAt());
//        self::assertGreaterThanOrEqual($before, $result->getUpdatedAt());
//        self::assertLessThanOrEqual($after, $result->getUpdatedAt());
//    }
//
//    // --- getValue() ---
//
//    public function testGetValueReturnsExistingVariable(): void
//    {
//        $existing = (new SiteVariable())->setName('key')->setValue('stored');
//
//        $this->repository
//            ->expects(self::once())
//            ->method('findOneBy')
//            ->with(['name' => 'key'])
//            ->willReturn($existing);
//
//        $this->entityManager->expects(self::never())->method('persist');
//
//        $result = $this->repository->getValue('key');
//
//        self::assertSame($existing, $result);
//        self::assertSame('stored', $result->getValue());
//    }
//
//    public function testGetValueCreatesAndPersistsVariableWhenNotFound(): void
//    {
//        $this->repository
//            ->expects(self::once())
//            ->method('findOneBy')
//            ->with(['name' => 'missing'])
//            ->willReturn(null);
//
//        $this->entityManager->expects(self::once())->method('persist');
//        $this->entityManager->expects(self::once())->method('flush');
//
//        $result = $this->repository->getValue('missing');
//
//        self::assertSame('missing', $result->getName());
//        self::assertSame('', $result->getValue());
//        self::assertNotNull($result->getUpdatedAt());
//    }
//
//    // --- findOneByName() ---
//
//    public function testFindOneByNameReturnsMatchingVariable(): void
//    {
//        $variable = (new SiteVariable())->setName('foo')->setValue('bar');
//
//        $this->repository
//            ->expects(self::once())
//            ->method('findOneByName')
//            ->with('foo')
//            ->willReturn($variable);
//
//        $result = $this->repository->findOneByName('foo');
//
//        self::assertSame($variable, $result);
//    }
//
//    public function testFindOneByNameReturnsNullWhenNotFound(): void
//    {
//        $this->repository
//            ->method('findOneByName')
//            ->with('unknown')
//            ->willReturn(null);
//
//        self::assertNull($this->repository->findOneByName('unknown'));
//    }
}

