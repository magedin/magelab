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
use MagedIn\Lab\CommandBuilder\DockerCompose;
use MagedIn\Lab\CommandBuilder\DockerComposePhpExec;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TlsVersionCommand extends Command
{
    /**
     * @var DockerComposePhpExec
     */
    private DockerComposePhpExec $dockerComposePhpExec;

    public function __construct(
        DockerComposePhpExec $dockerComposePhpExec,
        string $name = null
    ) {
        parent::__construct($name);
        $this->dockerComposePhpExec = $dockerComposePhpExec;
        $this->setHidden(true);
    }

    protected function configure()
    {
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $content = '<?php
$ch = curl_init("https://www.howsmyssl.com/a/check");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$data = curl_exec($ch);
curl_close($ch);

$json = json_decode($data);
echo $json->tls_version;
';
        $file = '/var/www/scripts/tls-version.php';
        $subcommands = ["echo $content > $file"];
        $command = $this->dockerComposePhpExec->build($subcommands);
        $process = Process::run($command);

        echo $process->getOutput();

        return Command::SUCCESS;
    }
}
