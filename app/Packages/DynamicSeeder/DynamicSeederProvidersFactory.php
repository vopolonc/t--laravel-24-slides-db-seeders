<?php

namespace App\Packages\DynamicSeeder;

use App\Packages\DynamicSeeder\DataProviders\DynamicSeederDataProvider;


class DynamicSeederProvidersFactory
{
    protected DynamicSeederDataProvider $provider;

    public function __construct(string $providerClass, array $config = [])
    {
        $this->provider = new $providerClass($config);
    }

    public function getProvider()
    {
        return $this->provider;
    }
}