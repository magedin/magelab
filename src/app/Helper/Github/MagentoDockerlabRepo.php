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

namespace MagedIn\Lab\Helper\Github;

class MagentoDockerlabRepo implements RepoInterface
{
    use RepoTrait;

    const GITHUB_REPO_CODE = 'magento-dockerlab';
}
