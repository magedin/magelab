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

namespace MagedIn\Lab\Command\Magento;

use MagedIn\Lab\Command\Command;
use MagedIn\Lab\CommandBuilder\Magento;
use MagedIn\Lab\Helper\Console\NonDefaultOptions;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    /**
     * @var Magento
     */
    private Magento $magentoCommandBuilder;

    public function __construct(
        Magento $magentoCommandBuilder,
        string $name = null
    ) {
        $this->magentoCommandBuilder = $magentoCommandBuilder;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->addOption(
            'base_url',
            'u',
            InputOption::VALUE_OPTIONAL,
            'The base URL Magento 2 will use.',
            'magento2.dev'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $subcommand = $this->getInstallOptions($input, $output);
        array_unshift($subcommand, 'setup:install');
        $command = $this->magentoCommandBuilder->build($subcommand);

        Process::run($command, [
            'tty' => true,
            'callback' => function ($type, $buffer) use ($output) {
                $output->writeln($buffer);
            },
        ]);

        return Command::SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string[]
     */
    private function getInstallOptions(InputInterface $input, OutputInterface $output): array
    {
        return [
            /* Database Options */
            "--db-host=".getenv('MYSQL_HOST'),
            "--db-name=".getenv('MYSQL_DATABASE'),
            "--db-user=".getenv('MYSQL_USER'),
            "--db-password=".getenv('MYSQL_PASSWORD'),

            /* Base URL */
            "--base-url=".sprintf("https://%s/", $input->getOption('base_url')),
            "--backend-frontname=".getenv('ADMIN_URI'),

            /* Admin Configuration */
            "--admin-firstname=".getenv('ADMIN_FIRSTNAME'),
            "--admin-lastname=".getenv('ADMIN_LASTNAME'),
            "--admin-email=".getenv('ADMIN_EMAIL'),
            "--admin-user=".getenv('ADMIN_USER'),
            "--admin-password=".getenv('ADMIN_PASSWORD'),

            /* Cache Backend Configuration */
            "--cache-backend=".getenv('CACHE_BACKEND'),
            "--cache-backend-redis-server=".getenv('CACHE_BACKEND_REDIS_SERVER'),
            "--cache-backend-redis-db=".getenv('CACHE_BACKEND_REDIS_DB'),

            /* Page Cache Configuration */
            "--page-cache=".getenv('PAGE_CACHE'),
            "--page-cache-redis-server=".getenv('PAGE_CACHE_REDIS_SERVER'),
            "--page-cache-redis-db=".getenv('PAGE_CACHE_REDIS_DB'),

            /* Session Configuration */
            "--session-save=".getenv('SESSION_SAVE'),
            "--session-save-redis-host=".getenv('SESSION_SAVE_REDIS_HOST'),
            "--session-save-redis-log-level=".getenv('SESSION_SAVE_REDIS_LOG_LEVEL'),
            "--session-save-redis-db=".getenv('SESSION_SAVE_REDIS_DB'),

            /* Search Engine */
            "--search-engine=".getenv('SEARCH_ENGINE'),
            "--elasticsearch-host=".getenv('ES_HOST'),

            /* General Configuration */
            "--use-rewrites=1",
        ];
    }
}
