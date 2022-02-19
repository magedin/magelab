<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2022 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

declare(strict_types=1);

namespace MagedIn\Lab\Helper\Generator;

use Faker\Factory;

class Uuid
{
    /**
     * @var Factory
     */
    private Factory $fakerFactory;

    public function __construct(
        Factory $fakerFactory
    ) {
        $this->fakerFactory = $fakerFactory;
    }

    /**
     * @return string
     */
    public function generate(): string
    {
        $faker = $this->fakerFactory->create();
        return $faker->uuid();
    }
}