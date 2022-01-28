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

namespace MagedIn\Lab\CommandBuilder\DockerCompose;

use MagedIn\Lab\Helper\DockerLab\BasePath;
use MagedIn\Lab\Helper\OperatingSystem;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;

class DockerComposeFiles
{
    /**
     * @var OperatingSystem
     */
    private OperatingSystem $operatingSystem;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var Dotenv
     */
    private Dotenv $dotenv;

    public function __construct(
        OperatingSystem $operatingSystem,
        Filesystem $filesystem,
        Dotenv $dotenv
    ) {
        $this->operatingSystem = $operatingSystem;
        $this->filesystem = $filesystem;
        $this->dotenv = $dotenv;
    }

    /**
     * @var array
     */
    private array $loadedFiles = [];

    /**
     * @return string[]
     */
    public function load(): array
    {
        if (empty($this->loadedFiles)) {
            $this->loadFiles();
        }
        return $this->loadedFiles;
    }

    /**
     * @return void
     */
    private function loadFiles(): void
    {
        $rootDir = BasePath::getAbsoluteRootDir();
        $isMacOs = $this->operatingSystem->isMacOs();

        $this->loadedFiles = ['docker-compose.yml'];
        if (true === $isMacOs) {
            $this->loadedFiles[] = 'docker-compose.dev.mac.yml';
        } else {
            $this->loadedFiles[] = 'docker-compose.dev.yml';
        }

        $this->dotenv->loadEnv($rootDir . '/.env');

        $file = 'docker-compose.mailhog.yml';
        $this->loadedFiles[] = $file;

        $validations = [
            [
                'file' => 'docker-compose.kibana.yml',
                'variable' => 'SERVICE_KIBANA_ENABLED',
            ], [
                'file' => 'docker-compose.jenkins.yml',
                'variable' => 'SERVICE_JENKINS_ENABLED',
            ], [
                'file' => 'docker-compose.pmm.yml',
                'variable' => 'SERVICE_PMM_ENABLED',
            ], [
                'file' => 'docker-compose.rabbitmq.yml',
                'variable' => 'SERVICE_RABBITMQ_ENABLED',
            ], [
                'file' => 'docker-compose.varnish.yml',
                'variable' => 'SERVICE_VARNISH_ENABLED',
            ], [
                'file' => 'docker-compose.custom.yml',
            ],
        ];

        foreach ($validations as $validation) {
            $this->loadFileIfValidated($validation);
        }
    }

    /**
     * @param array $validation
     * @return void
     */
    private function loadFileIfValidated(array $validation): void
    {
        $file = $validation['file'];
        $variable = $validation['variable'] ?? null;

        if ($this->validateService($file, $variable)) {
            $this->loadedFiles[] = $file;
        }
    }

    /**
     * @param string $filename
     * @param string|null $variable
     * @return bool
     */
    private function validateService(string $filename, string $variable = null): bool
    {
        $rootDir = BasePath::getAbsoluteRootDir();

        if (!$this->filesystem->exists($rootDir . DIRECTORY_SEPARATOR . $filename)) {
            return false;
        }

        if ($variable && !$this->validateEnvironmentVariable($variable)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $name
     * @return bool
     */
    private function validateEnvironmentVariable(string $name): bool
    {
        if (!isset($_ENV[$name])) {
            return false;
        }
        if (!$_ENV[$name]) {
            return false;
        }
        return true;
    }
}