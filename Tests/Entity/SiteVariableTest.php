<?php

declare(strict_types=1);

namespace Golpilolz\DBConfigs\Tests\Entity;

use Golpilolz\DBConfigs\Entity\SiteVariable;
use PHPUnit\Framework\TestCase;

class SiteVariableTest extends TestCase
{
    public function testNewEntityGettersDoNotThrow(): void
    {
        $entity = new SiteVariable();

        self::assertNull($entity->getName());
        self::assertSame('', $entity->getValue());
        self::assertNull($entity->getUpdatedAt());
    }

    public function testSettersAreChainable(): void
    {
        $date = new \DateTime('2024-01-15 12:00:00');

        $entity = (new SiteVariable())
            ->setName('key')
            ->setValue('value')
            ->setUpdatedAt($date);

        self::assertSame('key', $entity->getName());
        self::assertSame('value', $entity->getValue());
        self::assertSame($date, $entity->getUpdatedAt());
    }

    public function testLifecycleCallbacksRefreshUpdatedAt(): void
    {
        $old = new \DateTime('2000-01-01');

        foreach (['onPrePersist', 'onPreUpdate'] as $callback) {
            $entity = (new SiteVariable())->setUpdatedAt($old);
            $before = new \DateTime();

            $entity->$callback();

            self::assertGreaterThanOrEqual($before, $entity->getUpdatedAt(), $callback);
        }
    }
}
