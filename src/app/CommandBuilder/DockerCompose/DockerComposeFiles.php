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
use MagedIn\Lab\Helper\DockerLab\DirList;
use MagedIn\Lab\Helper\DockerLab\DockerCompose\CustomFileWriter;
use MagedIn\Lab\Helper\OperatingSystem;
use MagedIn\Lab\Model\Config\ConfigFacade;
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
     * @var ConfigFacade
     */
    private ConfigFacade $configFacade;

    /**
     * @var DirList
     */
    private DirList $dirList;

    private CustomFileWriter $customFileWriter;

    public function __construct(
        OperatingSystem $operatingSystem,
        Filesystem $filesystem,
        ConfigFacade $configFacade,
        DirList $dirList,
        CustomFileWriter $customFileWriter
    ) {
        $this->operatingSystem = $operatingSystem;
        $this->filesystem = $filesystem;
        $this->configFacade = $configFacade;
        $this->dirList = $dirList;
        $this->customFileWriter = $customFileWriter;
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
     * @return void
     */
    private function collectFiles(): void
    {
        $isMacOs = $this->operatingSystem->isMacOs();
        $this->loadedFiles = ['docker-compose.yml'];
        if (true === $isMacOs) {
            $this->loadedFiles[] = 'docker-compose.dev.mac.yml';
        } else {
            $this->loadedFiles[] = 'docker-compose.dev.yml';
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
        $filePattern = 'docker-compose.%s.yml';
        $validations = [];
        foreach ($services as $service) {
            $validations[] = [
                'file' => sprintf($filePattern, $service),
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
        if (!$this->filesystem->exists($this->dirList->getRootDir() . DS . $filename)) {
            return false;
        }
        return true;
    }

    /**
     * @return void
     */
    private function loadCustomerDockerComposeFile()
    {
        $filename = 'docker-compose.custom.yml';
        if (!$this->validateFile($filename)) {
            $this->customFileWriter->write();
        }
        $this->loadedFiles[] = $filename;
    }
}
