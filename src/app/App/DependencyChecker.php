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

namespace MagedIn\Lab\App;

use MagedIn\Lab\Exception\MissingDependencyException;
use MagedIn\Lab\Model\Process;

class DependencyChecker
{
    /**
     * @return void
     * @throws MissingDependencyException
     */
    public function check(): void
    {
        $this->checkDockerInstallation();
        $this->checkDockerComposeInstallation();
        $this->checkPhpInstallation();
    }

    /**
     * @return void
     * @throws MissingDependencyException
     */
    private function checkDockerInstallation()
    {
        $process = Process::run(['which', 'docker'], ['pty' => true]);
        $output = trim($process->getOutput());
        if (empty($output)) {
            throw new MissingDependencyException('Docker is required on this machine.');
        }
    }

    /**
     * @return void
     * @throws MissingDependencyException
     */
    private function checkDockerComposeInstallation()
    {
        $process = Process::run(['which', 'docker-compose'], ['pty' => true]);
        $output = trim($process->getOutput());
        if (empty($output)) {
            throw new MissingDependencyException('Docker-compose is required on this machine.');
        }
    }

    /**
     * @return void
     * @throws MissingDependencyException
     */
    private function checkPhpInstallation()
    {
        $process = Process::run(['php', '--version'], ['pty' => true]);
        $output = trim($process->getOutput());
        $exception = new MissingDependencyException('PHP >= 7.4 is required on this machine.');

        if (empty($output)) {
            throw $exception;
        }

        preg_match('/^PHP.([0-9]\.[0-9]\.[0-9]{1,3})/', $output, $matches);
        $compare = version_compare($matches[1], '7.4');
        if ($compare < 0) {
            throw $exception;
        }
    }
}
