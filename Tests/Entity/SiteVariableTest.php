<?php

declare(strict_types=1);

namespace Golpilolz\DBConfigs\Tests\Entity;

use Golpilolz\DBConfigs\Entity\SiteVariable;
use PHPUnit\Framework\TestCase;

class SiteVariableTest extends TestCase
{
    private SiteVariable $entity;

    protected function setUp(): void
    {
        $this->entity = new SiteVariable();
    }

    public function testSetAndGetName(): void
    {
        $result = $this->entity->setName('my_key');

        self::assertSame('my_key', $this->entity->getName());
        self::assertInstanceOf(SiteVariable::class, $result);
    }

//    public function testSetAndGetValue(): void
//    {
//        $result = $this->entity->setValue('hello world');
//
//        self::assertSame('hello world', $this->entity->getValue());
//        self::assertInstanceOf(SiteVariable::class, $result);
//    }
//
//    public function testSetAndGetUpdatedAt(): void
//    {
//        $date = new \DateTime('2024-01-15 12:00:00');
//        $result = $this->entity->setUpdatedAt($date);
//
//        self::assertSame($date, $this->entity->getUpdatedAt());
//        self::assertInstanceOf(SiteVariable::class, $result);
//    }
//
//    public function testGetNameReturnsNullWhenNotSet(): void
//    {
//        self::assertNull($this->entity->getName());
//    }
//
//    public function testGetValueReturnsNullWhenNotSet(): void
//    {
//        self::assertNull($this->entity->getValue());
//    }
//
//    public function testGetUpdatedAtReturnsNullWhenNotSet(): void
//    {
//        self::assertNull($this->entity->getUpdatedAt());
//    }
//
//    public function testSettersAreChainable(): void
//    {
//        $date = new \DateTime();
//
//        $result = $this->entity
//            ->setName('key')
//            ->setValue('value')
//            ->setUpdatedAt($date);
//
//        self::assertSame('key', $this->entity->getName());
//        self::assertSame('value', $this->entity->getValue());
//        self::assertSame($date, $this->entity->getUpdatedAt());
//        self::assertInstanceOf(SiteVariable::class, $result);
//    }
//
//    public function testOnPrePersistSetsUpdatedAt(): void
//    {
//        $before = new \DateTime();
//        $this->entity->onPrePersist();
//        $after = new \DateTime();
//
//        $updatedAt = $this->entity->getUpdatedAt();
//
//        self::assertNotNull($updatedAt);
//        self::assertGreaterThanOrEqual($before, $updatedAt);
//        self::assertLessThanOrEqual($after, $updatedAt);
//    }
//
//    public function testOnPreUpdateSetsUpdatedAt(): void
//    {
//        $before = new \DateTime();
//        $this->entity->onPreUpdate();
//        $after = new \DateTime();
//
//        $updatedAt = $this->entity->getUpdatedAt();
//
//        self::assertNotNull($updatedAt);
//        self::assertGreaterThanOrEqual($before, $updatedAt);
//        self::assertLessThanOrEqual($after, $updatedAt);
//    }
//
//    public function testOnPrePersistOverwritesExistingUpdatedAt(): void
//    {
//        $old = new \DateTime('2000-01-01');
//        $this->entity->setUpdatedAt($old);
//
//        $this->entity->onPrePersist();
//
//        self::assertNotSame($old, $this->entity->getUpdatedAt());
//    }
//
//    public function testOnPreUpdateOverwritesExistingUpdatedAt(): void
//    {
//        $old = new \DateTime('2000-01-01');
//        $this->entity->setUpdatedAt($old);
//
//        $this->entity->onPreUpdate();
//
//        self::assertNotSame($old, $this->entity->getUpdatedAt());
//    }
//
//    public function testEmptyStringIsValidValue(): void
//    {
//        $this->entity->setValue('');
//
//        self::assertSame('', $this->entity->getValue());
//    }
//
//    public function testLongStringValue(): void
//    {
//        $long = str_repeat('a', 65535);
//        $this->entity->setValue($long);
//
//        self::assertSame($long, $this->entity->getValue());
//    }
}

