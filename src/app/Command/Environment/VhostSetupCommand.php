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

use MagedIn\Lab\Command\Command;
use MagedIn\Lab\Helper\DockerLab\DirList;
use MagedIn\Lab\Helper\DockerLab\Template\TemplateLoader;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class VhostSetupCommand extends Command
{
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

    public function __construct(
        DirList $dirList,
        Filesystem $filesystem,
        TemplateLoader $templateLoader,
        string $name = null
    ) {
        parent::__construct($name);
        $this->dirList = $dirList;
        $this->filesystem = $filesystem;
        $this->templateLoader = $templateLoader;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument(
            'domains',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'The domain to setup on Nginx.'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $domains = $input->getArgument('domains');
        foreach ($domains as $domain) {
            $this->setupDomain($domain);
        }
        return Command::SUCCESS;
    }

    /**
     * @param string $domain
     * @return void
     */
    private function setupDomain(string $domain)
    {
        $upstream = md5($domain);
        $this->dirList->getNginxConfigDir($domain);

        foreach ($this->getVhostFilesMap($domain) as $templateFile => $configFile) {
            $content = $this->templateLoader->load($templateFile, [
                'host'     => $domain,
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
}
