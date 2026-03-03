<?php

namespace App\Interfaces\v1\Scenarios;

interface ScenarioInterface
{
    /**
     * Набор допустимых сценариев сущности.
     */
    public function getScenarios(): array;
}
