<?php

declare(strict_types=1);

namespace MagedIn\Lab\Helper\Github;

class DownloadRepo implements RepoInterface
{
    use RepoTrait;

    const GITHUB_REPO_CODE = 'magento-opensource-releases';
}
