<?php

namespace App\Service;

use Tienvx\Bundle\MbtBundle\Service\ConfigLoaderInterface;

class ConfigLoader implements ConfigLoaderInterface
{
    public function getGenerator(): string
    {
        return 'random';
    }

    public function getMaxSteps(): int
    {
        return 150;
    }

    public function getMaxTransitionCoverage(): float
    {
        return 100;
    }

    public function getMaxPlaceCoverage(): float
    {
        return 100;
    }

    public function getReducer(): string
    {
        return 'random';
    }

    public function getNotifyChannels(): array
    {
        return [];
    }
}
