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
use MagedIn\Lab\Console\Input\ArrayInputFactory;
use MagedIn\Lab\Helper\DockerLab\DirList;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VhostSslSetupCommand extends Command
{
    /**
     * @var DirList
     */
    private DirList $dirList;

    private ArrayInputFactory $arrayInputFactory;

    public function __construct(
        DirList $dirList,
        ArrayInputFactory $arrayInputFactory,
        string $name = null
    ) {
        parent::__construct($name);
        $this->dirList = $dirList;
        $this->arrayInputFactory = $arrayInputFactory;
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
            $this->setupDomainSsl($domain, $output);
        }
        return Command::SUCCESS;
    }

    /**
     * @param string $domain
     * @param OutputInterface $output
     * @return void
     */
    private function setupDomainSsl(string $domain, OutputInterface $output)
    {
        $sslCommand = $this->getApplication()->find('ssl');

        $files = [
            'cert' => $this->getCertificatesDir() . DS . "$domain.pem",
            'key'  => $this->getCertificatesDir() . DS . "$domain-key.pem",
        ];

        /** @var ArrayInput $input */
        $input = $this->arrayInputFactory->create([
            'domains' => [$domain],
            '--cert-file' => $files['cert'],
            '--key-file' => $files['key'],
        ]);
        $sslCommand->run($input, $output);
    }

    /**
     * @return string
     */
    private function getCertificatesDir(): string
    {
        $varDir = $this->dirList->getVarDir();
        $sslDir = $varDir . DS . 'ssl';
        return $sslDir . DS . 'certificates';
    }
}
