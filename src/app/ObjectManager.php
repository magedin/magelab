<?php

declare(strict_types=1);

namespace MagedIn\Lab;

use DI\Container;
use DI\ContainerBuilder;
use MagedIn\Lab\Console\CommandsBuilder;

class ObjectManager
{
    /**
     * @var ContainerBuilder
     */
    private ContainerBuilder $containerBuilder;

    /**
     * @var Container
     */
    private Container $container;

    /**
     * @var ObjectManager|null
     */
    private static ?ObjectManager $objectManager = null;

    public function __construct()
    {
        $this->containerBuilder = new ContainerBuilder();
        $this->container = $this->containerBuilder->build();
    }

    /**
     * @return static
     */
    public static function getInstance(): self
    {
        if (!self::$objectManager) {
            self::$objectManager = new self();
        }
        return self::$objectManager;
    }

    /**
     * @param string $name
     * @return CommandsBuilder|mixed
     */
    public function get(string $name)
    {
        return $this->container->get($name);
    }

    /**
     * @param string $name
     * @param array $params
     * @return mixed|string
     */
    public function create(string $name, array $params = [])
    {
        return $this->container->make($name, $params);
    }
}
