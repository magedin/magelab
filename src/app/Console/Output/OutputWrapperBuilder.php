<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2021 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

declare(strict_types=1);

namespace MagedIn\Lab\Console\Output;

use MagedIn\Lab\ObjectManager;
use Symfony\Component\Console\Output\OutputInterface;

class OutputWrapperBuilder
{
    /**
     * @param OutputInterface $output
     * @return OutputWrapper
     */
    public function build(OutputInterface $output): OutputWrapper
    {
        return ObjectManager::getInstance()->create(OutputWrapper::class, ['output' => $output]);
    }
}