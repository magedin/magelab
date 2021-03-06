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

namespace MagedIn\Lab\Helper\DockerLab\DockerCompose;

use MagedIn\Lab\Helper\DockerLab\DirList;
use MagedIn\Lab\Helper\OperatingSystem;
use MagedIn\Lab\Model\Config\ConfigFacade;

class DockerComposeFilesCollector
{
    /**
     * @var OperatingSystem
     */
    private OperatingSystem $operatingSystem;

    /**
     * @var ConfigFacade
     */
    private ConfigFacade $configFacade;

    /**
     * @var DockerComposeFileValidator
     */
    private DockerComposeFileValidator $dockerComposeFileValidator;

    /**
     * @var DockerComposeFilenameResolver
     */
    private DockerComposeFilenameResolver $dockerComposeFilenameResolver;

    /**
     * @var DirList
     */
    private DirList $dirList;

    public function __construct(
        OperatingSystem $operatingSystem,
        ConfigFacade $configFacade,
        DockerComposeFileValidator $dockerComposeFileValidator,
        DockerComposeFilenameResolver $dockerComposeFilenameResolver,
        DirList $dirList
    ) {
        $this->operatingSystem = $operatingSystem;
        $this->configFacade = $configFacade;
        $this->dockerComposeFileValidator = $dockerComposeFileValidator;
        $this->dockerComposeFilenameResolver = $dockerComposeFilenameResolver;
        $this->dirList = $dirList;
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
        /** Always append the main docker-compose file. */
        $this->appendFile($this->dockerComposeFilenameResolver->getDockerComposeMainFilename());
        if (true === $this->operatingSystem->isMacOs()) {
            $this->appendFile($this->dockerComposeFilenameResolver->resolveDockerComposeServiceFilename('dev.mac'));
            $this->appendFile($this->dockerComposeFilenameResolver->resolveDockerComposeServiceFilename('mac'));
        } elseif (true === $this->operatingSystem->isLinux()) {
            $this->appendFile($this->dockerComposeFilenameResolver->resolveDockerComposeServiceFilename('dev'));
            /** @todo Create a custom docker-compose.linux.yml file if it does not exit. */
            $this->appendFile($this->dockerComposeFilenameResolver->resolveDockerComposeServiceFilename('linux'));
        } else {
            $this->appendFile($this->dockerComposeFilenameResolver->resolveDockerComposeServiceFilename('dev'));
            /** @todo Create a custom docker-compose.windows.yml file if it does not exit. */
            $this->appendFile($this->dockerComposeFilenameResolver->resolveDockerComposeServiceFilename('windows'));
        }
        $this->loadOptionalServices();
        $this->appendFile($this->dockerComposeFilenameResolver->getDockerComposeCustomFilename());
    }

    /**
     * @param string $filename
     * @return void
     */
    private function appendFile(string $filename): void
    {
        $filename = $this->dirList->absolutePathFromRoot($filename);
        if ($this->dockerComposeFileValidator->validate($filename)) {
            $this->loadedFiles[] = $filename;
        }
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
                'file' => $this->dockerComposeFilenameResolver->resolveDockerComposeServiceFilename($service),
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
        if ($isEnabled === true) {
            $this->appendFile($file);
        }
    }
}
