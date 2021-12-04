<?php

declare(strict_types=1);

namespace MageLab\Config\Github;

class DownloadRepo
{
    const GITHUB_API_URL = 'https://api.github.com';
    const GITHUB_BASE_URL = 'https://github.com';
    const GITHUB_REPO_OWNER = 'magedin';
    const GITHUB_REPO_CODE = 'magento-opensource-releases';

    /**
     * @return string
     */
    public static function getRepoBaseUrl(): string
    {
        return self::getBaseUrl() . '/' . self::getRepoCode();
    }

    /**
     * @return string
     */
    public static function getBaseUrl(): string
    {
        return self::GITHUB_BASE_URL;
    }

    /**
     * @return string
     */
    public static function getApiUrl(): string
    {
        return self::GITHUB_API_URL;
    }

    /**
     * @return string
     */
    public static function getRepoApiUrl(): string
    {
        return self::getApiUrl() . '/repos/' . self::getRepoCode();
    }

    /**
     * @return string
     */
    public static function getRepoOwner(): string
    {
        return self::GITHUB_REPO_OWNER;
    }

    /**
     * @return string
     */
    public static function getRepoName(): string
    {
        return self::GITHUB_REPO_CODE;
    }

    /**
     * @return string
     */
    public static function getRepoCode(): string
    {
        return self::getRepoOwner() . '/' . self::getRepoName();
    }

    /**
     * @param string $filename
     * @return string
     */
    public static function buildDownloadUrl(string $filename): string
    {
        return self::getRepoBaseUrl() . '/archive/' . $filename;
    }

    /**
     * @return string
     */
    public static function getRepoTagsUrl(array $params = []): string
    {
        $queryString = http_build_query($params);
        return self::getRepoApiUrl() . '/tags' . ($queryString ? '?' . $queryString : null);
    }
}
