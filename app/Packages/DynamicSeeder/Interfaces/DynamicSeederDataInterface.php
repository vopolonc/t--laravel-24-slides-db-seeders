<?php

namespace App\Packages\DynamicSeeder\Interfaces;

interface DynamicSeederDataInterface
{
    public function getData(): array;

    public function parseFile(string $filename): static;

    public function parseString(string $input): static;
}