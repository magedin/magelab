<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2021 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

namespace MagedIn\Lab\Helper\Github;

interface RepoInterface
{
    const GITHUB_API_URL = 'https://api.github.com';
    const GITHUB_BASE_URL = 'https://github.com';
    const GITHUB_SSH_URL = 'git@github.com';
    const GITHUB_REPO_OWNER = 'magedin';

    const DEFAULT_TIMEOUT = 10;
}