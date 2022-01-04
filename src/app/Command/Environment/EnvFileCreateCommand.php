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

namespace MagedIn\Lab\Command\Environment;

use MagedIn\Lab\Helper\DockerLab\BasePath;
use MagedIn\Lab\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;

class EnvFileCreateCommand extends Command
{
    const ARG_SILENT = 'silent';

    /**
     * @var string
     */
    private string $envFileName = '.env';

    private array $envVariables = [
        'services' => [
            'SERVICE_MAILHOG_ENABLED' => [
                'question' => 'Do you want to use MailHog?',
                'default' => 'y',
                'type' => 'confirm',
            ],
            'SERVICE_KIBANA_ENABLED' => [
                'question' => 'Do you want to use Kibana?',
                'default' => 'y',
                'type' => 'confirm',
            ],
            'SERVICE_RABBITMQ_ENABLED' => [
                'question' => 'Do you want to use RabbitMQ?',
                'default' => 'y',
                'type' => 'confirm',
            ],
        ],
        'magento' => [
            'BASE_URL' => [
                'question' => 'Please inform the Base URL for your store:',
                'default' => 'magento2.test',
            ],
        ],
    ];

    /**
     * @var array|array[]
     */
    private array $dumpVariables = [
        'services' => [],
        'magento' => [
            'BASE_URL' => null,
            'ADMIN_URI' => 'backend',
            'ADMIN_FIRSTNAME' => 'Admin',
            'ADMIN_LASTNAME' => 'User',
            'ADMIN_USER' => 'admin.user',
            'ADMIN_EMAIL' => 'admin.user@example.com',
            'ADMIN_PASSWORD' => 'Password@123',
        ],
        'db' => [
            'MYSQL_HOST' => 'db',
            'MYSQL_ROOT_PASSWORD' => 'magento',
            'MYSQL_DATABASE' => 'magento',
            'MYSQL_USER' => 'magento',
            'MYSQL_PASSWORD' => 'magento',
            'MYSQL_DUMP_DIR' => '/var/dumps',
        ],
        'redis' => [
            'CACHE_BACKEND' => 'redis',
            'CACHE_BACKEND_REDIS_SERVER' => 'redis',
            'CACHE_BACKEND_REDIS_DB' => 0,

            'PAGE_CACHE' => 'redis',
            'PAGE_CACHE_REDIS_SERVER' => 'redis',
            'PAGE_CACHE_REDIS_DB' => 1,

            'SESSION_SAVE' => 'redis',
            'SESSION_SAVE_REDIS_HOST' => 'redis',
            'SESSION_SAVE_REDIS_LOG_LEVEL' => 4,
            'SESSION_SAVE_REDIS_DB' => 2,
        ],
        'rabbitmq' => [
            'RABBITMQ_HOST' => 'rabbitmq',
            'RABBITMQ_PORT' => 5672,
            'RABBITMQ_DEFAULT_USER' => 'magento',
            'RABBITMQ_DEFAULT_PASS' => 'magento',
            'RABBITMQ_DEFAULT_VHOST' => 'magento',
        ],
        'search engine' => [
            'SEARCH_ENGINE' => 'elasticsearch7',
            'ES_HOST' => 'elasticsearch',
        ],
    ];

    protected function configure()
    {
        $this->addOption(
            self::ARG_SILENT,
            's',
            InputOption::VALUE_NONE,
            "Silently exists if an error happens.",
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->createEnvFile();
        $this->populateFile($input, $output);
        return Command::SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function populateFile(InputInterface $input, OutputInterface $output)
    {
        $dotEnv = new Dotenv();
        $dotEnv->load($this->getEnvFileLocation());
        $this->populateServices($input, $output);
        $this->populateMagento($input, $output);
        $this->dumpEnvFile();
    }

    /**
     * @return void
     */
    private function dumpEnvFile(): void
    {
        /** @var Filesystem $filesystem */
        $filesystem = ObjectManager::getInstance()->create(Filesystem::class);
        foreach ($this->dumpVariables as $serviceKey => $serviceVariables) {
            $printFooter = false;
            if (empty($serviceVariables)) {
                continue;
            }

            $printHeader = true;
            foreach ($serviceVariables as $name => $value) {
                if (isset($_ENV[$name])) {
                    continue;
                }
                if (true === $printHeader) {
                    $this->printDivisor("$serviceKey Variables");
                    $printHeader = false;
                    $printFooter = true;
                }

                $filesystem->appendToFile($this->getEnvFileLocation(), "$name=$value\n");
            }

            if (true === $printFooter) {
                $this->printDivisor(null, 3);
            }
        }
    }

    /**
     * @param string|null $label
     * @param int $linebreaks
     * @return void
     */
    private function printDivisor(string $label = null, int $linebreaks = 2)
    {
        /** @var Filesystem $filesystem */
        $filesystem = ObjectManager::getInstance()->create(Filesystem::class);

        if (empty($label)) {
            $filesystem->appendToFile($this->getEnvFileLocation(), "\n");
        } else {
            $label = strtoupper($label);
        }

        $pad = str_pad("# $label", 80, '-');
        $filesystem->appendToFile($this->getEnvFileLocation(), $pad);
        $pad = str_pad("", $linebreaks, "\n");
        $filesystem->appendToFile($this->getEnvFileLocation(), $pad);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function populateServices(InputInterface $input, OutputInterface $output): void
    {
        $this->populateNode('services', $input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function populateMagento(InputInterface $input, OutputInterface $output): void
    {
        $this->populateNode('magento', $input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function populateNode(string $node, InputInterface $input, OutputInterface $output): void
    {
        foreach ($this->envVariables[$node] as $serviceKey => $serviceData) {
            if (isset($_ENV[$serviceKey])) {
                continue;
            }
            $question = $serviceData['question'];
            $default = $serviceData['default'];
            $type = $serviceData['type'] ?? 'question';

            if ($type === 'confirm') {
                $answer = $this->confirm($input, $output, "<question>$question</question>", $default);
            } else {
                $answer = $this->ask($input, $output, "<question>$question</question>", $default);
            }

            $this->dumpVariables[$node][$serviceKey] = $answer;
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $question
     * @param string $default
     * @return bool
     */
    private function confirm(InputInterface $input, OutputInterface $output, string $question, string $default)
    {
        $answer = $this->ask($input, $output, $question, $default);
        if ('y' === strtolower(substr($answer, 0, 1))) {
            return true;
        };
        return false;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $question
     * @param string $default
     * @return string
     */
    private function ask(InputInterface $input, OutputInterface $output, string $question, string $default): string
    {
        $dialog = $this->getHelper('question');
        /** @var Question $question */
        $questionObject = ObjectManager::getInstance()->create(Question::class, [
            'question' => "<question>$question</question>",
            'default' => $default
        ]);
        return $dialog->ask($input, $output, $questionObject);
    }

    /**
     * @return void
     */
    private function createEnvFile(): void
    {
        $envFileLocation = $this->getEnvFileLocation();
        $envFile = realpath($envFileLocation);

        $filesystem = new Filesystem();
        if (!$envFile || !$filesystem->exists($envFile)) {
            $filesystem->touch($envFileLocation);
        }
    }

    /**
     * @return string
     */
    private function getEnvFileLocation(): string
    {
        $basePath = BasePath::getRootDir();
        return realpath($basePath) . '/' . $this->envFileName;
    }
}
