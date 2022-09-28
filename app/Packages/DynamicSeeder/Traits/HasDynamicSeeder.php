<?php

namespace App\Packages\DynamicSeeder\Traits;

use App\Packages\DynamicSeeder\ModelMappings\DynamicSeederModelMapping;

trait HasDynamicSeeder
{
    /**
     * @throws \Exception
     */
    public static function getDynamicSeederMapping(): DynamicSeederModelMapping
    {
        return DynamicSeederModelMapping::determineMapper(static::class);
    }
}