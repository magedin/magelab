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

namespace MagedIn\Lab\Helper;

use MagedIn\Lab\CommandBuilder\DockerCompose;
use MagedIn\Lab\Model\Process;

class DockerServiceState
{
    const STATE_UNDETECTED = 0;
    const STATE_STOPPED = 1;
    const STATE_RUNNING = 2;
    const STATE_DOWN = 3;
    const STATE_PARTIALLY_RUNNING = 4;

    /**
     * @var int
     */
    private int $currentState = self::STATE_UNDETECTED;

    /**
     * @var DockerCompose
     */
    private DockerCompose $dockerComposeCommandBuilder;

    public function __construct(
        DockerCompose $dockerComposeCommandBuilder
    ) {
        $this->dockerComposeCommandBuilder = $dockerComposeCommandBuilder;
    }

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        $this->initialize();
        return $this->getState() === self::STATE_RUNNING;
    }

    /**
     * @return bool
     */
    public function isStopped(): bool
    {
        $this->initialize();
        return $this->getState() === self::STATE_STOPPED;
    }

    /**
     * @return bool
     */
    public function isDown(): bool
    {
        $this->initialize();
        return $this->getState() === self::STATE_DOWN;
    }

    /**
     * @return bool
     */
    public function isPartiallyRunning(): bool
    {
        $this->initialize();
        return $this->getState() === self::STATE_PARTIALLY_RUNNING;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->currentState;
    }

    /**
     * Initialize the detection.
     * @return void
     */
    private function initialize(): void
    {
        if ($this->currentState !== self::STATE_UNDETECTED) {
            return;
        }

        $services = $this->getServices();
        if (empty($services)) {
            $this->currentState = self::STATE_DOWN;
            return;
        }

        $total = count($services);
        $running = count(array_filter($services));
        $stopped = $total - $running;

        if ($total - $running == 0) {
            $this->currentState = self::STATE_RUNNING;
            return;
        }

        if ($total - $stopped == 0) {
            $this->currentState = self::STATE_STOPPED;
            return;
        }

        $this->currentState = self::STATE_PARTIALLY_RUNNING;
    }

    /**
     * @return array
     */
    private function getServices(): array
    {
        $command = $this->dockerComposeCommandBuilder->build(['ps', '--all']);
        $process = Process::run($command);
        $rows = explode(PHP_EOL, $process->getOutput());
        /** Strip away the first two lines (header and dash separator). */
        array_shift($rows);
        array_shift($rows);
        /** Remove all empty lines. */
        $rows = array_filter($rows);

        $services = [];
        foreach ($rows as $row) {
            $row = preg_replace('/\s+/', '|', trim($row));
            $parts = array_filter(explode('|', $row));
            $service = array_shift($parts);
            $isRunning = $this->scanUpState($parts);
            $services[$service] = $isRunning;
        }

        return $services;
    }

    /**
     * @param array $parts
     * @return bool
     */
    private function scanUpState(array $parts = []): bool
    {
        foreach ($parts as $part) {
            if ('up' == substr(strtolower($part), 0, 2)) {
                return true;
            }
        }
        return false;
    }
}
