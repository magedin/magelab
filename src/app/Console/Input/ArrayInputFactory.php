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

namespace MagedIn\Lab\Console\Input;

use MagedIn\Lab\ObjectManager;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;

class ArrayInputFactory
{
    /**
     * @param array $parameters
     * @param InputDefinition|null $definition
     * @return ArrayInput
     */
    public function create(array $parameters = [], InputDefinition $definition = null): ArrayInput
    {
        /** @var ArrayInput $input */
        return ObjectManager::getInstance()->create(ArrayInput::class, [
            'parameters' => $parameters,
            'definition' => $definition,
        ]);
    }
}
