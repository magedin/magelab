<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2022 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

namespace MagedIn\Lab\Helper\DockerServiceState;

use MagedIn\Lab\Helper\OperatingSystem;

class ServiceExtractor
{
    /**
     * @var OperatingSystem
     */
    private OperatingSystem $operatingSystem;

    /**
     * @param OperatingSystem $operatingSystem
     */
    public function __construct(
        OperatingSystem $operatingSystem
    ) {
        $this->operatingSystem = $operatingSystem;
    }

    /**
     * @param string $output
     * @return array
     */
    public function getServices(string $output): array
    {
        $rows = explode(PHP_EOL, $output);
        $rows = $this->cleanOutput($rows);

        $services = [];
        foreach ($rows as $row) {
            list($service, $isRunning) = $this->getRowInformation($row);
            $services[$service] = $isRunning;
        }
        return $services;
    }

    /**
     * @param array $outputRows
     * @return array
     */
    private function cleanOutput(array $outputRows): array
    {
        /** Remove all empty lines. */
        $outputRows = array_filter($outputRows);
        /** Strip away the first line (header). */
        array_shift($outputRows);
        if (!$this->operatingSystem->isMacOs()) {
            /** Strip away the first two lines (header and dash separator). */
            array_shift($outputRows);
        }
        return (array) $outputRows;
    }

    /**
     * @param string $row
     * @return array
     */
    private function getRowInformation(string $row): array
    {
        $row = preg_replace('/\s+/', '|', trim($row));
        $parts = array_filter(explode('|', $row));
        $service = array_shift($parts);
        $isRunning = $this->scanUpState($parts);
        return [$service, $isRunning];
    }

    /**
     * @param array $parts
     * @return bool
     */
    private function scanUpState(array $parts = []): bool
    {
        $runningState = ['up', 'running'];
        foreach ($parts as $part) {
            if (in_array(strtolower(trim((string) $part)), $runningState)) {
                return true;
            }
        }
        return false;
    }
}
