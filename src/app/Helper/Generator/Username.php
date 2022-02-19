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

class Username
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
        $username = preg_replace('/[^a-zA-Z\s]/', '', $faker->name());
        $username = preg_replace('/\s/', '_', $username);
        return strtolower($username);
    }
}