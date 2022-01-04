<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2022 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

declare(strict_types=1);

namespace MagedIn\Lab\Helper\DockerLab\Template;

use MagedIn\Lab\Helper\DockerLab\DirList;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Filesystem\Filesystem;

class TemplateLoader
{
    /**
     * @var DirList
     */
    private DirList $dirList;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    public function __construct(
        DirList $dirList,
        Filesystem $filesystem
    ) {
        $this->dirList = $dirList;
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $template
     * @param array $replacements
     * @return string
     */
    public function load(string $template, array $replacements = []): string
    {
        $content = $this->getTemplateContent($template);
        return $this->processReplacements($content, $replacements);
    }

    /**
     * @param string $template
     * @return string
     */
    private function getTemplateFile(string $template): string
    {
        if (!$this->validateTemplate($template)) {
            throw new RuntimeException("Template file '$template' does not exist.");
        }
        return $this->buildTemplateFileLocation($template);
    }

    /**
     * @param string $template
     * @return string
     */
    private function getTemplateContent(string $template): string
    {
        return file_get_contents($this->getTemplateFile($template));
    }

    /**
     * @param string $content
     * @param array $replacements
     * @return string
     */
    private function processReplacements(string $content, array $replacements = []): string
    {
        foreach ($replacements as $placeholder => $value) {
            $content = str_replace($this->buildPlaceholder($placeholder), $value, $content);
        }
        return $content;
    }

    /**
     * @param string $name
     * @return string
     */
    private function buildPlaceholder(string $name): string
    {
        return '{{' . $name . '}}';
    }

    /**
     * @param string $template
     * @return bool
     */
    private function validateTemplate(string $template): bool
    {
        $templateFile = $this->buildTemplateFileLocation($template);
        return $this->filesystem->exists($templateFile) && is_readable($templateFile);
    }

    /**
     * @param string $template
     * @return string
     */
    private function buildTemplateFileLocation(string $template): string
    {
        $templateDir = $this->dirList->getTemplateDir();
        return $templateDir . DS . $template;
    }
}
