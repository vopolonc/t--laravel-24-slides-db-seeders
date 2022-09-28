<?php

namespace App\Packages\DynamicSeeder\DataProviders;

use App\Packages\DynamicSeeder\Interfaces\DynamicSeederDataInterface;

abstract class DynamicSeederDataProvider implements DynamicSeederDataInterface
{
    protected ?array $data = null;

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @param array $config
     * @return void
     * @throws \Exception
     */
    protected function populateConfig(array $config = [])
    {
        try {
            foreach ($config as $propertyName => $propertyValue) {
                $this->{$propertyName} = $propertyValue;
            }
        } catch (\Throwable) {
            abort(500, sprintf('Property [%s] in [%s] does not exist or is in wrong format.', $propertyName, static::class));
        }
    }
}