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

use MagedIn\Lab\Helper\DockerLab\DirList;
use MagedIn\Lab\Helper\DockerLab\DockerCompose\CustomFileWriter;
use MagedIn\Lab\Helper\DockerLab\EnvFileCreator;
use MagedIn\Lab\Helper\OperatingSystem;
use MagedIn\Lab\Model\Config\ConfigFacade;
use Symfony\Component\Filesystem\Filesystem;

class DockerComposeFiles
{
    /**
     * @var string
     */
    private string $mainDockerComposeFile = 'docker-compose.yml';

    /**
     * @var OperatingSystem
     */
    private OperatingSystem $operatingSystem;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var ConfigFacade
     */
    private ConfigFacade $configFacade;

    /**
     * @var DirList
     */
    private DirList $dirList;

    /**
     * @var CustomFileWriter
     */
    private CustomFileWriter $customFileWriter;

    /**
     * @var EnvFileCreator
     */
    private EnvFileCreator $envFileCreator;

    public function __construct(
        OperatingSystem $operatingSystem,
        Filesystem $filesystem,
        ConfigFacade $configFacade,
        DirList $dirList,
        CustomFileWriter $customFileWriter,
        EnvFileCreator $envFileCreator
    ) {
        $this->operatingSystem = $operatingSystem;
        $this->filesystem = $filesystem;
        $this->configFacade = $configFacade;
        $this->dirList = $dirList;
        $this->customFileWriter = $customFileWriter;
        $this->envFileCreator = $envFileCreator;
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
            $this->collectFiles();
        }
        return $this->loadedFiles;
    }

    /**
     * @return string
     */
    public function getDockerComposeMainFilename(): string
    {
        return $this->mainDockerComposeFile;
    }

    /**
     * @return string
     */
    public function getDockerComposeCustomFilename(): string
    {
        return $this->buildDockerComposeServiceFilename('custom');
    }

    /**
     * @param string $service
     * @return string
     */
    public function buildDockerComposeServiceFilename(string $service): string
    {
        $service = preg_replace('[â-zA-Z0-9\.\_\-]', '', $service);
        return 'services' . DS . sprintf('docker-compose.%s.yml', $service);
    }

    /**
     * @return void
     */
    private function collectFiles(): void
    {
        $isMacOs = $this->operatingSystem->isMacOs();
        $this->loadedFiles = [$this->mainDockerComposeFile];
        if (true === $isMacOs) {
            $this->loadedFiles[] = 'services' . DS . 'docker-compose.dev.mac.yml';
        } else {
            $this->loadedFiles[] = 'services' . DS . 'docker-compose.dev.yml';
        }
        $this->loadOptionalServices();
        $this->loadCustomerDockerComposeFile();
    }

    /**
     * @return void
     */
    private function loadOptionalServices(): void
    {
        $services = $this->configFacade->services()->getNames();
        $validations = [];
        foreach ($services as $service) {
            $validations[] = [
                'file' => $this->buildDockerComposeServiceFilename($service),
                'is_enabled' => $this->configFacade->services()->isEnabled($service),
            ];
        }
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
        $file      = $validation['file'];
        $isEnabled = $validation['is_enabled'] ?? false;
        if ($this->validateFile($file) && $isEnabled === true) {
            $this->loadedFiles[] = $file;
        }
    }

    /**
     * @param string $filename
     * @return bool
     */
    private function validateFile(string $filename): bool
    {
        $absoluteFilename = $this->dirList->getRootDir() . DS . $filename;
        if (!$this->filesystem->exists($absoluteFilename)) {
            return false;
        }
        if (!is_readable($absoluteFilename)) {
            return false;
        }
        return true;
    }

    /**
     * @return void
     */
    private function loadCustomerDockerComposeFile()
    {
        $filename = $this->getDockerComposeCustomFilename();
        if (!$this->validateFile($filename)) {
            $this->customFileWriter->write();
        }
        $this->loadedFiles[] = $filename;
        $this->envFileCreator->create();
    }
}
