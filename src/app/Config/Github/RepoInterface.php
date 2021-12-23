<?php

namespace MageLab\Config\Github;

interface RepoInterface
{
    const GITHUB_API_URL = 'https://api.github.com';
    const GITHUB_BASE_URL = 'https://github.com';
    const GITHUB_SSH_URL = 'git@github.com';
    const GITHUB_REPO_OWNER = 'magedin';

    const DEFAULT_TIMEOUT = 10;
}