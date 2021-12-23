<?php

declare(strict_types=1);

namespace MageLab\Config\Github;

class MagentoDockerlabRepo implements RepoInterface
{
    use RepoTrait;

    const GITHUB_REPO_CODE = 'magento-dockerlab';
}
