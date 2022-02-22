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

namespace MagedIn\Lab\CommandExecutor\Magento;

use MagedIn\Lab\CommandBuilder\Magento;
use MagedIn\Lab\CommandExecutor\CommandExecutorAbstract;
use MagedIn\Lab\CommandExecutor\Magento\Install\DefaultOptions;
use MagedIn\Lab\Model\Process;

class Install extends CommandExecutorAbstract
{
    /**
     * @var array|array[]
     */
    protected array $config = [
        'base_url' => null,
    ];

    /**
     * @var Magento
     */
    private Magento $magentoCommandBuilder;

    /**
     * @var DefaultOptions
     */
    private DefaultOptions $defaultOptions;

    public function __construct(
        Magento $magentoCommandBuilder,
        DefaultOptions $defaultOptions
    ) {
        $this->magentoCommandBuilder = $magentoCommandBuilder;
        $this->defaultOptions = $defaultOptions;
    }

    /**
     * @inheritDoc
     */
    protected function doExecute(array $commands = [], array $config = [])
    {
        $baseUrl = $config['base_url'] ?? $this->defaultOptions->getDefaultInstallBaseUrl();
        $baseUrl = rtrim($baseUrl, '/') . '/';
        $subcommand = $this->defaultOptions->getInstallOptions($baseUrl);
        array_unshift($subcommand, 'setup:install');
        $command = $this->magentoCommandBuilder->build($subcommand);
        $options = [
            'tty' => true,
            'callback' => $config['callback'] ?? null,
        ];
        Process::run($command, $options);
    }
}
