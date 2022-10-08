<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2022 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

namespace MagedIn\Lab\Exception;

use Symfony\Component\Console\Exception\ExceptionInterface;

class DockerContainersNotRunningException extends DockerException implements ExceptionInterface
{
}
