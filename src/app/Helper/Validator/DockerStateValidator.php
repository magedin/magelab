<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2022 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

namespace MagedIn\Lab\Helper\Validator;

use MagedIn\Lab\Exception\DockerException;
use MagedIn\Lab\Helper\DockerIp;

class DockerStateValidator
{
    /**
     * @var DockerIp
     */
    private DockerIp $dockerIp;

    /**
     * @param DockerIp $dockerIp
     */
    public function __construct(
        DockerIp $dockerIp
    ) {
        $this->dockerIp = $dockerIp;
    }

    /**
     * @return bool
     * @throws DockerException
     */
    public function validate(): bool
    {
        $this->dockerIp->get();
        return true;
    }
}
