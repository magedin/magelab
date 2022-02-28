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

namespace MagedIn\Lab\Helper\DockerLab\DockerCompose;

use MagedIn\Lab\Helper\DockerLab\Installation;

class CustomFileManager
{
    /**
     * @var CustomFileWriter
     */
    private CustomFileWriter $customFileWriter;

    /**
     * @var DockerComposeFileValidator
     */
    private DockerComposeFileValidator $fileValidator;

    /**
     * @var Installation
     */
    private Installation $installation;

    public function __construct(
        CustomFileWriter $customFileWriter,
        DockerComposeFileValidator $fileValidator,
        Installation $installation
    ) {
        $this->customFileWriter = $customFileWriter;
        $this->fileValidator = $fileValidator;
        $this->installation = $installation;
    }

    /**
     * @param array $config
     * @param bool $overwrite
     * @return void
     */
    public function write(array $config = [], bool $overwrite = false): void
    {
        if (!$this->installation->isInstalled()) {
            return;
        }

        if ($overwrite === false && $this->fileValidator->validate($this->customFileWriter->getConfigFilename())) {
            return;
        }

        $this->customFileWriter->write($config);
    }

    /**
     * @return array
     */
    public function loadCurrentContent(): array
    {
        return $this->customFileWriter->loadCurrentContent();
    }
}
