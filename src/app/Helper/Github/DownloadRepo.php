<?php

declare(strict_types=1);

namespace MageLab\Helper\Github;

class DownloadRepo implements RepoInterface
{
    use RepoTrait;

    const GITHUB_REPO_CODE = 'magento-opensource-releases';
}
