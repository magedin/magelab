<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2022 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

namespace MagedIn\Lab\Helper\Validator;

use MagedIn\Lab\Exception\DockerNotRunningException;
use MagedIn\Lab\Helper\DockerServiceState;

class DockerContainerStateValidator
{
    /**
     * @var DockerServiceState
     */
    private DockerServiceState $dockerServiceState;

    /**
     * @param DockerServiceState $dockerServiceState
     */
    public function __construct(
        DockerServiceState $dockerServiceState
    ) {
        $this->dockerServiceState = $dockerServiceState;
    }

    /**
     * @return bool
     * @throws DockerNotRunningException
     */
    public function validate(): bool
    {
        if ($this->dockerServiceState->isRunning()) {
            return true;
        }
        throw new DockerNotRunningException('Docker containers are not running. They need to be running in order to execute this command.');
    }
}
