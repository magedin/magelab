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
use MagedIn\Lab\Helper\DockerLab\EnvFileCreator;
use MagedIn\Lab\Helper\OperatingSystem;
use MagedIn\Lab\Model\Config\ConfigFacade;
use Symfony\Component\Filesystem\Filesystem;

class DockerComposeFilesCollector
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

    /**
     * @var DockerComposeFileValidator
     */
    private DockerComposeFileValidator $dockerComposeFileValidator;

    private DockerComposeFilenameResolver $dockerComposeFilenameResolver;

    public function __construct(
        OperatingSystem $operatingSystem,
        Filesystem $filesystem,
        ConfigFacade $configFacade,
        DirList $dirList,
        CustomFileWriter $customFileWriter,
        EnvFileCreator $envFileCreator,
        DockerComposeFileValidator $dockerComposeFileValidator,
        DockerComposeFilenameResolver $dockerComposeFilenameResolver
    ) {
        $this->operatingSystem = $operatingSystem;
        $this->filesystem = $filesystem;
        $this->configFacade = $configFacade;
        $this->dirList = $dirList;
        $this->customFileWriter = $customFileWriter;
        $this->envFileCreator = $envFileCreator;
        $this->dockerComposeFileValidator = $dockerComposeFileValidator;
        $this->dockerComposeFilenameResolver = $dockerComposeFilenameResolver;
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
            $this->appendFile($this->dockerComposeFilenameResolver->resolveDockerComposeServiceFilename('linux'));
        } else {
            $this->appendFile($this->dockerComposeFilenameResolver->resolveDockerComposeServiceFilename('dev'));
            $this->appendFile($this->dockerComposeFilenameResolver->resolveDockerComposeServiceFilename('windows'));
        }
        $this->loadOptionalServices();
        $this->loadCustomerDockerComposeFile();
    }

    /**
     * @param string $filename
     * @return void
     */
    private function appendFile(string $filename): void
    {
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

    /**
     * @return void
     */
    private function loadCustomerDockerComposeFile()
    {
        $this->customFileWriter->write();
        $this->appendFile($this->dockerComposeFilenameResolver->getDockerComposeCustomFilename());
        $this->envFileCreator->create();
    }
}
