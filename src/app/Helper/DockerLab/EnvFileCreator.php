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

namespace MagedIn\Lab\Helper\DockerLab;

use Symfony\Component\Filesystem\Filesystem;

class EnvFileCreator
{
    /**
     * @var string
     */
    private string $envFilename = '.env';

    /**
     * @var array|array[]
     */
    private array $dumpVariables = [
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
        'kibana' => [
            'ELASTICSEARCH_HOSTS' => 'http://elasticsearch:9200',
            'SERVER_NAME' => 'localhost',
            'SERVER_HOST' => '0.0.0.0',
            'MONITORING_ENABLED' => 'true'
        ],
        'aws' => [
            'AWS_S3_BUCKET' => null,
            'AWS_S3_PATH' => null,
            'AWS_PROFILE' => null,

        ],
        'search engine' => [
            'SEARCH_ENGINE' => 'elasticsearch7',
            'ES_HOST' => 'elasticsearch',
        ],
        'blackfire' => [
            'BLACKFIRE_CLIENT_ID' => null,
            'BLACKFIRE_CLIENT_TOKEN' => null,
            'BLACKFIRE_SERVER_ID' => null,
            'BLACKFIRE_SERVER_TOKEN' => null,
            'BLACKFIRE_LOG_LEVEL' => 4,
        ],
        'newrelic' => [
            'NR_INSTALL_SILENT' => true,
            'NR_INSTALL_KEY' => null,
        ],
    ];

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var DirList
     */
    private DirList $dirList;

    public function __construct(
        Filesystem $filesystem,
        DirList $dirList
    ) {
        $this->filesystem = $filesystem;
        $this->dirList = $dirList;
    }

    /**
     * @param bool $force
     * @return void
     */
    public function create(bool $force = false)
    {
        if ($force === false && $this->fileExists()) {
            return;
        }
        $this->createEnvFile();
        $this->dumpEnvFile();
    }

    /**
     * @return void
     */
    private function dumpEnvFile(): void
    {
        foreach ($this->dumpVariables as $serviceKey => $serviceVariables) {
            $printFooter = false;
            if (empty($serviceVariables)) {
                continue;
            }

            $printHeader = true;
            foreach ($serviceVariables as $name => $value) {
                if (true === $printHeader) {
                    $this->printDivisor("$serviceKey Variables");
                    $printHeader = false;
                    $printFooter = true;
                }

                $value = $value ?: null;
                $this->filesystem->appendToFile($this->getEnvFileLocation(), "$name=$value\n");
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
        if (empty($label)) {
            $this->filesystem->appendToFile($this->getEnvFileLocation(), "\n");
        } else {
            $label = strtoupper($label);
        }

        $pad = str_pad("# $label ", 80, '-');
        $this->filesystem->appendToFile($this->getEnvFileLocation(), $pad);
        $pad = str_pad("", $linebreaks, "\n");
        $this->filesystem->appendToFile($this->getEnvFileLocation(), $pad);
    }

    /**
     * @return void
     */
    private function createEnvFile(): void
    {
        $this->filesystem->touch($this->getEnvFileLocation());
    }

    /**
     * @return string
     */
    private function getEnvFileLocation(): string
    {
        return $this->dirList->getRootDir() . DS . $this->envFilename;
    }

    /**
     * @return bool
     */
    private function fileExists(): bool
    {
        return $this->filesystem->exists($this->getEnvFileLocation());
    }
}
