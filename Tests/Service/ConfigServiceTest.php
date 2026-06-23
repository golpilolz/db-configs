<?php

declare(strict_types=1);

namespace Golpilolz\DBConfigs\Tests\Service;

use Golpilolz\DBConfigs\Entity\SiteVariable;
use Golpilolz\DBConfigs\Repository\SiteVariableRepository;
use Golpilolz\DBConfigs\Service\ConfigService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConfigServiceTest extends TestCase
{
    private SiteVariableRepository&MockObject $repository;
    private ConfigService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(SiteVariableRepository::class);
        $this->service = new ConfigService($this->repository);
    }

    public function testGetReturnsStoredValue(): void
    {
        $variable = (new SiteVariable())->setName('site_name')->setValue('My Site');

        $this->repository
            ->expects(self::once())
            ->method('getValue')
            ->with('site_name')
            ->willReturn($variable);

        self::assertSame('My Site', $this->service->get('site_name'));
    }

    public function testGetReturnsEmptyStringWhenValueIsEmpty(): void
    {
        $variable = (new SiteVariable())->setName('empty_key')->setValue('');

        $this->repository
            ->method('getValue')
            ->willReturn($variable);

        self::assertSame('', $this->service->get('empty_key'));
    }

    public function testSetCallsRepositorySave(): void
    {
        $this->repository
            ->expects(self::once())
            ->method('save')
            ->with('site_name', 'My Site');

        $this->service->set('site_name', 'My Site');
    }

    public function testGetArrayDecodesJsonValue(): void
    {
        $data = ['foo' => 'bar', 'baz' => 'qux'];
        $variable = (new SiteVariable())->setName('my_array')->setValue(json_encode($data));

        $this->repository
            ->method('getValue')
            ->willReturn($variable);

        self::assertSame($data, $this->service->getArray('my_array'));
    }

    public function testGetArrayReturnsFallbackWhenValueIsEmpty(): void
    {
        $variable = (new SiteVariable())->setName('missing')->setValue('');

        $this->repository
            ->method('getValue')
            ->willReturn($variable);

        $fallback = ['default' => 'value'];
        self::assertSame($fallback, $this->service->getArray('missing', $fallback));
    }

    public function testGetArrayReturnsFallbackWhenValueIsInvalidJson(): void
    {
        $variable = (new SiteVariable())->setName('bad_json')->setValue('not-json');

        $this->repository
            ->method('getValue')
            ->willReturn($variable);

        $fallback = ['x' => 'y'];
        self::assertSame($fallback, $this->service->getArray('bad_json', $fallback));
    }

    public function testGetArrayReturnsEmptyArrayAsFallbackByDefault(): void
    {
        $variable = (new SiteVariable())->setName('missing')->setValue('');

        $this->repository
            ->method('getValue')
            ->willReturn($variable);

        self::assertSame([], $this->service->getArray('missing'));
    }

    public function testGetArrayReturnsFallbackWhenJsonIsScalar(): void
    {
        $variable = (new SiteVariable())->setName('scalar')->setValue('"just a string"');

        $this->repository
            ->method('getValue')
            ->willReturn($variable);

        $fallback = ['fallback' => 'used'];
        self::assertSame($fallback, $this->service->getArray('scalar', $fallback));
    }

    public function testSetArrayEncodesAndSaves(): void
    {
        $data = ['key' => 'val'];

        $this->repository
            ->expects(self::once())
            ->method('save')
            ->with('my_key', json_encode($data, JSON_THROW_ON_ERROR));

        $this->service->setArray('my_key', $data);
    }

    public function testSetArrayWithEmptyArraySavesEmptyJsonObject(): void
    {
        $this->repository
            ->expects(self::once())
            ->method('save')
            ->with('empty', '[]');

        $this->service->setArray('empty', []);
    }

    public function testAllReturnsNameValueMap(): void
    {
        $v1 = (new SiteVariable())->setName('a')->setValue('1');
        $v2 = (new SiteVariable())->setName('b')->setValue('2');

        $this->repository
            ->expects(self::once())
            ->method('findAll')
            ->willReturn([$v1, $v2]);

        self::assertSame(['a' => '1', 'b' => '2'], $this->service->all());
    }

    public function testAllReturnsEmptyArrayWhenNoVariables(): void
    {
        $this->repository
            ->method('findAll')
            ->willReturn([]);

        self::assertSame([], $this->service->all());
    }

    public function testAllConvertsValueToString(): void
    {
        $v = (new SiteVariable())->setName('num')->setValue('42');

        $this->repository
            ->method('findAll')
            ->willReturn([$v]);

        $result = $this->service->all();

        self::assertIsString($result['num']);
        self::assertSame('42', $result['num']);
    }
}

