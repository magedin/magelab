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
use MagedIn\Lab\Helper\DockerLab\DirList;
use MagedIn\Lab\Helper\DockerLab\DockerCompose\CustomFileManager;
use MagedIn\Lab\Helper\DockerLab\EnvFileCreator;
use MagedIn\Lab\Helper\DockerLab\EnvLoader;
use MagedIn\Lab\Helper\User\Home\Initializer as UserHomeInitializer;

class Initializer
{
    /**
     * @var DependencyChecker
     */
    private DependencyChecker $dependencyChecker;

    /**
     * @var EnvLoader
     */
    private EnvLoader $envLoader;

    /**
     * @var DirList
     */
    private DirList $dirList;

    /**
     * @var CustomFileManager
     */
    private CustomFileManager $customFileManager;

    /**
     * @var EnvFileCreator
     */
    private EnvFileCreator $envFileCreator;

    /**
     * @var UserHomeInitializer
     */
    private UserHomeInitializer $userHomeInitializer;

    public function __construct(
        DependencyChecker $dependencyChecker,
        EnvLoader $envLoader,
        DirList $dirList,
        CustomFileManager $customFileManager,
        EnvFileCreator $envFileCreator,
        UserHomeInitializer $userHomeInitializer
    ) {
        $this->dependencyChecker = $dependencyChecker;
        $this->envLoader = $envLoader;
        $this->dirList = $dirList;
        $this->customFileManager = $customFileManager;
        $this->envFileCreator = $envFileCreator;
        $this->userHomeInitializer = $userHomeInitializer;
    }

    /**
     * @return void
     * @throws MissingDependencyException
     */
    public function initialize()
    {
        $this->dependencyChecker->check();
        $this->envLoader->load();
        $this->dirList->init();
        $this->customFileManager->write();
        $this->envFileCreator->create();
        $this->userHomeInitializer->initialize();
    }
}
