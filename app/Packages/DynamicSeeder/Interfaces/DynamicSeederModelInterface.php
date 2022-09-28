<?php

namespace App\Packages\DynamicSeeder\Interfaces;

interface DynamicSeederModelInterface
{
    public function getModelKey(): string;

    public function getModelAttributes(): array;
}