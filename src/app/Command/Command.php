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

namespace MagedIn\Lab\Command;

use MagedIn\Lab\Exception\DockerNotRunningException;
use MagedIn\Lab\Helper\DockerLab\BasePath;
use MagedIn\Lab\Helper\Validator\DockerStateValidator;
use MagedIn\Lab\ObjectManager;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends BaseCommand
{
    /**
     * Commands that can only run inside a DockerLab project must set this option to true.
     * @var bool
     */
    protected bool $isPrivate = true;

    /**
     * Most of the commands requires docker, some of them don't.
     * @var bool
     */
    protected bool $requiresDocker = true;

    /**
     * @return bool
     */
    public function isEnabled()
    {
        if ($this->isPrivate && !BasePath::isValid()) {
            return false;
        }
        return parent::isEnabled();
    }

    /**
     * @return bool
     */
    public function isProxyCommand(): bool
    {
        return false;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws DockerNotRunningException
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->validateDockerRequirement();
    }

    /**
     * @return void
     * @throws DockerNotRunningException
     */
    private function validateDockerRequirement()
    {
        if ($this->requiresDocker) {
            /** @var DockerStateValidator $dockerStateValidator */
            $dockerStateValidator = ObjectManager::getInstance()->get(DockerStateValidator::class);
            $dockerStateValidator->validate();
        }
    }
}
