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

use Exception;
use MagedIn\Lab\Command\Command;
use MagedIn\Lab\Console\Input\ArrayInputFactory;
use MagedIn\Lab\Helper\DockerLab\DirList;
use MagedIn\Lab\Helper\DockerLab\Template\TemplateLoader;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class VhostSetupCommand extends Command
{
    const ARGUMENT_DOMAIN = 'domain';
    const ARGUMENT_MAGE_RUN_TYPE = 'type';
    const ARGUMENT_MAGE_RUN_CODE = 'code';

    /**
     * @var DirList
     */
    private DirList $dirList;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var TemplateLoader
     */
    private TemplateLoader $templateLoader;

    /**
     * @var ArrayInputFactory
     */
    private ArrayInputFactory $arrayInputFactory;

    public function __construct(
        DirList $dirList,
        Filesystem $filesystem,
        TemplateLoader $templateLoader,
        ArrayInputFactory $arrayInputFactory,
        string $name = null
    ) {
        parent::__construct($name);
        $this->dirList = $dirList;
        $this->filesystem = $filesystem;
        $this->templateLoader = $templateLoader;
        $this->arrayInputFactory = $arrayInputFactory;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument(
            self::ARGUMENT_DOMAIN,
            InputArgument::REQUIRED,
            'The domain to setup on Nginx.'
        )->addOption(
            self::ARGUMENT_MAGE_RUN_TYPE,
            't',
            InputOption::VALUE_OPTIONAL,
            'The type of scope Magento will run. It is the same as MAGE_RUN_TYPE property.',
            'website'
        )->addOption(
            self::ARGUMENT_MAGE_RUN_CODE,
            'c',
            InputOption::VALUE_OPTIONAL,
            'The code of the scope Magento will run. It is the same as MAGE_RUN_CODE property.',
            'base'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $domain = $input->getArgument(self::ARGUMENT_DOMAIN);
        $type   = $input->getOption(self::ARGUMENT_MAGE_RUN_TYPE);
        $code   = $input->getOption(self::ARGUMENT_MAGE_RUN_CODE);
        $this->assertMageRunType($type);
        $this->setupCertificates($domain, $output);
        $this->setupDomain($domain, $type, $code);
        return Command::SUCCESS;
    }

    /**
     * @param string $domain
     * @param OutputInterface $output
     * @return void
     * @throws Exception
     */
    private function setupCertificates(string $domain, OutputInterface $output)
    {
        $sslCommand = $this->getApplication()->find('ssl:setup');
        $input = $this->arrayInputFactory->create([
            'domains' => [$domain],
        ]);
        $sslCommand->run($input, $output);
    }

    /**
     * @param string $domain
     * @param string $type
     * @param string $code
     * @return void
     */
    private function setupDomain(string $domain, string $type, string $code): void
    {
        $upstream = md5($domain);
        $this->dirList->getNginxConfigDir($domain);

        foreach ($this->getVhostFilesMap($domain) as $templateFile => $configFile) {
            $content = $this->templateLoader->load($templateFile, [
                'host'     => $domain,
                'run_type' => $type,
                'run_code' => $code,
                'upstream' => $upstream,
            ]);
            $file = $this->buildNginxConfigFileLocation($configFile);
            $this->filesystem->dumpFile($file, $content);
        }
    }

    /**
     * @param string $file
     * @return string
     */
    private function buildNginxConfigFileLocation(string $file): string
    {
        return $this->dirList->getNginxConfigDir() . DS . $file;
    }

    /**
     * @param string $domain
     * @return string[]
     */
    private function getVhostFilesMap(string $domain): array
    {
        return [
            'nginx' . DS . 'magento2.conf'      => $domain . DS . 'magento2.conf',
            'nginx' . DS . 'magento2-ssl.conf'  => $domain . DS . 'magento2-ssl.conf',
            'nginx' . DS . 'magento2-vars.conf' => $domain . DS . 'magento2-vars.conf',
            'nginx' . DS . 'upstream.conf'      => "$domain.conf",
        ];
    }

    /**
     * @param string $type
     * @return void
     */
    private function assertMageRunType(string $type): void
    {
        $allowedTypes = ['website', 'websites', 'store', 'stores'];
        if (!in_array($type, $allowedTypes)) {
            throw new InvalidOptionException(
                'Invalid type for MAGE_RUN_TYPE. Allowed types are: ' . implode(', ', $allowedTypes)
            );
        }
    }
}
