<?php

declare(strict_types=1);

namespace Golpilolz\DBConfigs\Tests;

use Golpilolz\DBConfigs\GolpilolzDBConfigs;
use Golpilolz\DBConfigs\Repository\SiteVariableRepository;
use Golpilolz\DBConfigs\Service\ConfigService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class GolpilolzDBConfigsTest extends TestCase
{
    private GolpilolzDBConfigs $bundle;

    protected function setUp(): void
    {
        $this->bundle = new GolpilolzDBConfigs();
    }

    public function testGetPathReturnsParentDirectory(): void
    {
        $path = $this->bundle->getPath();

        self::assertDirectoryExists($path);
        self::assertSame(
            realpath(\dirname(__DIR__)),
            realpath($path)
        );
    }

    public function testGetPathEndsWithBundleRootDirectory(): void
    {
        $path = $this->bundle->getPath();

        // The path must not point to the Resources/config subdirectory
        self::assertStringNotContainsString('Resources', $path);
    }

    public function testLoadExtensionImportsServicesConfig(): void
    {
        $container = new ContainerBuilder();
        $instanceof = [];
        $bundlePath = $this->bundle->getPath();
        $loader = new PhpFileLoader($container, new FileLocator($bundlePath));
        $configurator = new ContainerConfigurator($container, $loader, $instanceof, $bundlePath . '/placeholder.php', 'placeholder.php');

        $this->bundle->loadExtension([], $configurator, $container);

        self::assertTrue(
            $container->has(ConfigService::class),
            'ConfigService should be registered in the container after loadExtension'
        );
        self::assertTrue(
            $container->has(SiteVariableRepository::class),
            'SiteVariableRepository should be registered in the container after loadExtension'
        );
    }
}

