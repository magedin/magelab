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

use MagedIn\Lab\Helper\Magento\MagentoDirList;
use MagedIn\Lab\Model\Config\EnvironmentVariables;
use MagedIn\Lab\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class EnvFileCreateCommand extends Command
{
    const OPTION_DOMAIN = 'domain';
    const OPTION_ADMIN_FRONT_NAME = 'admin-front-name';
    const OPTION_CRYPT_KEY = 'crypt-key';
    const OPTION_MAGE_MODE = 'mage-mode';

    /**
     * @var EnvironmentVariables
     */
    private EnvironmentVariables $environmentVariables;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var MagentoDirList
     */
    private MagentoDirList $magentoDirList;

    /**
     * @param EnvironmentVariables $environmentVariables
     * @param Filesystem $filesystem
     * @param MagentoDirList $magentoDirList
     * @param string|null $name
     */
    public function __construct(
        EnvironmentVariables $environmentVariables,
        Filesystem $filesystem,
        MagentoDirList $magentoDirList,
        string $name = null
    ) {
        parent::__construct($name);
        $this->environmentVariables = $environmentVariables;
        $this->filesystem = $filesystem;
        $this->magentoDirList = $magentoDirList;
    }

    protected function configure()
    {
        $this->addOption(
            self::OPTION_DOMAIN,
            'd',
            InputOption::VALUE_REQUIRED,
            'The domain of the website.',
            'magento2.test'
        );
        $this->addOption(
            self::OPTION_ADMIN_FRONT_NAME,
            'a',
            InputOption::VALUE_REQUIRED,
            'The front name of the admin area.',
            'backend'
        );
        $this->addOption(
            self::OPTION_CRYPT_KEY,
            'c',
            InputOption::VALUE_OPTIONAL,
            'The crypt key for your installation (if you have one).',
            '{{YOUR CRYPT KEY}}'
        );
        $this->addOption(
            self::OPTION_MAGE_MODE,
            'm',
            InputOption::VALUE_OPTIONAL,
            'The MAGE_MODE of your Magento (Modes: default, production, or developer).',
            'developer'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $this->getEnvFilepath();
        if ($this->filesystem->exists($file)) {
            $output->writeln("The file '$file' already exists and will not be overwritten.");
            $file = $this->getEnvFilepath(true);
            $output->writeln("The new file '$file' will be created instead.");
        }
        $finalConfig = $this->getConfigString($input);
        file_put_contents($file, $finalConfig);
        return Command::SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    private function getConfigString(InputInterface $input): string
    {
        $baseConfig = $this->getBaseConfig($input);
        $parsedConfig = $this->parseConfiguration($baseConfig);
        $finalConfigString = implode("\n", $parsedConfig);
        return "<?php\nreturn [\n" . $finalConfigString . "\n];\n";
    }

    /**
     * @param array $data
     * @param array $return
     * @param int $spaces
     * @return array
     */
    private function parseConfiguration(array $data, array &$return = [], int $spaces = 4): array
    {
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $return[] = $this->buildLine($spaces, $key, $value);
            } else {
                $return[] = $this->buildLine($spaces, $key);
                $this->parseConfiguration($value, $return, $spaces+4);
                $return[] = $this->buildLine($spaces);
            }
        }
        return $return;
    }

    /**
     * @param int $spaces
     * @param string|null $key
     * @param string|int|null $value
     * @return string
     */
    private function buildLine(int $spaces, string $key = null, $value = null): string
    {
        $lineString = "";
        if ($key && null !== $value) {
            $lineString .= "'$key' => '$value',";
        } elseif ($key && null === $value) {
            $lineString .= "'$key' => [";
        } else {
            $lineString .= "],";
        }
        return str_repeat(' ', $spaces) . $lineString;
    }

    /**
     * @param InputInterface $input
     * @return array
     */
    private function getBaseConfig(InputInterface $input): array
    {
        return [
            'backend' => [
                'frontName' => $input->getOption(self::OPTION_ADMIN_FRONT_NAME),
            ],
            'queue' => [
                'amqp' => [
                    'host' => '',
                    'port' => '',
                    'user' => '',
                    'password' => '',
                    'virtualhost' => '/',
                    'ssl' => ''
                ]
            ],
            'db' => [
                'connection' => [
                    'default' => [
                        'host' => 'db',
                        'dbname' => $this->environmentVariables->get('MYSQL_DATABASE', 'magento'),
                        'username' => $this->environmentVariables->get('MYSQL_USER', 'magento'),
                        'password' => $this->environmentVariables->get('MYSQL_PASSWORD', 'magento'),
                        'model' => 'mysql4',
                        'engine' => 'innodb',
                        'initStatements' => 'SET NAMES utf8;',
                        'active' => '1'
                    ]
                ],
                'table_prefix' => ''
            ],
            'crypt' => [
                'key' => $input->getOption(self::OPTION_CRYPT_KEY)
            ],
            'resource' => [
                'default_setup' => [
                    'connection' => 'default'
                ]
            ],
            'x-frame-options' => 'SAMEORIGIN',
            'MAGE_MODE' => $input->getOption(self::OPTION_MAGE_MODE),
            'session' => [
                'save' => 'redis',
                'redis' => [
                    'host' => 'redis',
                    'port' => '6379',
                    'password' => '',
                    'timeout' => '2.5',
                    'persistent_identifier' => '',
                    'database' => '0',
                    'compression_threshold' => '2048',
                    'compression_library' => 'gzip',
                    'log_level' => '1',
                    'max_concurrency' => '6',
                    'break_after_frontend' => '5',
                    'break_after_adminhtml' => '30',
                    'first_lifetime' => '600',
                    'bot_first_lifetime' => '60',
                    'bot_lifetime' => '7200',
                    'disable_locking' => '0',
                    'min_lifetime' => '60',
                    'max_lifetime' => '2592000'
                ]
            ],
            'cache' => [
                'frontend' => [
                    'default' => [
                        'backend' => 'Cm_Cache_Backend_Redis',
                        'backend_options' => [
                            'server' => 'redis',
                            'port' => '6379',
                            'database' => '1'
                        ]
                    ],
                    'page_cache' => [
                        'backend' => 'Cm_Cache_Backend_Redis',
                        'backend_options' => [
                            'server' => 'redis',
                            'port' => '6379',
                            'database' => '2',
                            'compress_data' => '0'
                        ]
                    ]
                ]
            ],
            'http_cache_hosts' => [
            ],
            'cache_types' => [
                'config' => 1,
                'layout' => 0,
                'block_html' => 0,
                'collections' => 0,
                'reflection' => 0,
                'db_ddl' => 0,
                'eav' => 0,
                'customer_notification' => 0,
                'target_rule' => 0,
                'full_page' => 0,
                'config_integration' => 0,
                'config_integration_api' => 0,
                'translate' => 0,
                'config_webservice' => 0,
                'compiled_config' => 0,
                'vertex' => 0
            ],
            'install' => [
                'date' => date(\DateTimeInterface::RSS),
            ],
            'downloadable_domains' => [
            ],
            'system' => [
                'default' => [
                    'web' => [
                        'unsecure' => [
                            'base_url' => $this->getWebsiteDomain($input),
                            'base_link_url' => '{{unsecure_base_url}}',
                            'base_static_url' => '{{unsecure_base_url}}/static/',
                            'base_media_url' => '{{unsecure_base_url}}/media/',
                        ],
                        'secure' => [
                            'base_url' => $this->getWebsiteDomain($input),
                            'base_link_url' => '{{secure_base_url}}',
                            'base_static_url' => '{{secure_base_url}}/static/',
                            'base_media_url' => '{{secure_base_url}}/media/',
                        ],
                        'seo' => [
                            'use_rewrites' => '1'
                        ],
                        'cookie' => [
                            'cookie_lifetime' => 86400,
                            'cookie_path' => '',
                            'cookie_domain' => '',
                            'cookie_httponly' => 1,
                        ],
                    ],
                    'catalog' => [
                        'search' => [
                            'engine' => 'elasticsearch7',
                            'elasticsearch7_server_hostname' => 'elasticsearch',
                            'elasticsearch7_server_port' => '9200',
                            'elasticsearch7_index_prefix' => 'magento2',
                            'elasticsearch7_enable_auth' => '0',
                            'elasticsearch7_username' => '',
                            'elasticsearch7_password' => '',
                            'elasticsearch7_server_timeout' => '15',
                        ]
                    ],
                    'csp' => [
                        'mode' => [
                            'storefront' => [
                                'report_only' => 1,
                            ],
                            'admin' => [
                                'report_only' => 1,
                            ],
                        ],
                    ],
                    'dev' => [
                        'static' => [
                            'sign' => 0
                        ],
                        'js' => [
                            'merge_files' => 0,
                            'minify_files' => 0
                        ],
                        'css' => [
                            'merge_css_files' => 0
                        ],
                        'template' => [
                            'minify_html' => 0
                        ],
                    ],
                    'admin' => [
                        'security' => [
                            'session_lifetime' => '86400',
                            'password_is_forced' => 0,
                            'password_lifetime' => '',
                        ],
                        'url' => [
                            'use_custom_path' => 0,
                            'use_custom' => 0,
                        ],
                        'captcha' => [
                            'enable' => 0,
                        ]
                    ],
                    'customer' => [
                        'captcha' => [
                            'enable' => 0,
                        ]
                    ],
                ],
                'websites' => [],
                'stores' => [],
            ],
            'modules' => [
                'Magento_TwoFactorAuth' => 0,
            ]
        ];
    }

    /**
     * @param bool $unique
     * @return string
     */
    private function getEnvFilepath(bool $unique = false): string
    {
        $filename = 'env';
        $extension = 'php';
        if ($unique) {
            $filename .= '.' . date('YmdHis');
        }
        return $this->magentoDirList->getAppConfigDir() . $filename . '.' . $extension;
    }

    /**
     * @param InputInterface $input
     * @param string|null $uriPath
     * @return string
     */
    private function getWebsiteDomain(InputInterface $input, string $uriPath = null): string
    {
        $uri = "https://{$input->getOption(self::OPTION_DOMAIN)}";
        if ($uriPath) {
            $uri .= '/'.$uriPath;
        }
        return $uri.'/';
    }
}
