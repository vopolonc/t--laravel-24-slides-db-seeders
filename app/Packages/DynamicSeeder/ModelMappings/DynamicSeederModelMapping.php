<?php

namespace App\Packages\DynamicSeeder\ModelMappings;

use App\Packages\DynamicSeeder\Interfaces\DynamicSeederModelInterface;
use App\Packages\DynamicSeeder\Traits\HasDynamicSeeder;
use Illuminate\Database\Eloquent\Model;

abstract class DynamicSeederModelMapping implements DynamicSeederModelInterface
{
    protected string $modelKey = 'id';
    protected array $attributes = [];

    protected static ?array $modelMappings = null;

    /**
     * @throws \Exception
     */
    public static function determineMapper(string $modelClass): DynamicSeederModelMapping
    {
        # ensure that model uses proper trait
        if (!in_array(HasDynamicSeeder::class, class_uses_recursive($modelClass))) {
            throw new \Exception(
                sprintf('Model [%s] must use trait [%s] to use the DynamicSeeder functionality.', $modelClass, HasDynamicSeeder::class)
            );
        }

        # if model mapping class not present then take default one
        $mappingModelClass = self::getModelMapperMapping()[$modelClass] ?? self::getDefaultModelMapping();

        return new $mappingModelClass();
    }

    public function allowAttribute(string $attributeName): bool
    {
        if (empty($this->getModelAttributes())) {
            return true;
        }

        return in_array($attributeName, $this->getModelAttributes());
    }

    public static function getAttributesToCast(): array
    {
        return [];
    }

    public function inheritFromParent(): array
    {
        return [];
    }

    public function relatesTo(): array
    {
        return [];
    }

    public function isRelatesTo(string $attributeName): bool
    {
        return in_array($attributeName, array_keys($this->relatesTo()));
    }

    public function castValue(string $attributeName, mixed $value): mixed
    {
        $toCastAttributes = static::getAttributesToCast();
        if (in_array($attributeName, array_keys($toCastAttributes))) {
            return $toCastAttributes[$attributeName]($value);
        }

        return $value;
    }

    /**
     * @param string $alias
     * @return Model
     * @throws \Exception
     */
    public static function determineModel(string $alias): Model
    {
        $allowedModels = self::getModelMapping();

        if (!in_array($alias, array_keys($allowedModels))) {
            throw new \Exception(
                sprintf('Alias [%s] not found (not mapped) in config [%s]', $alias, 'dynamic-seeder.model_mapping')
            );
        }

        return new ($allowedModels[$alias]);
    }

    public static function getModelMapping(): array
    {
        if (self::$modelMappings !== null) {
            return self::$modelMappings;
        }

        self::setModelMapping();

        return self::getModelMapping();
    }

    /**
     * from:
     *      'user|users' => \App\Models\User::class,
     *
     * to:
     *      'user' => \App\Models\User::class,
     *      'users' => \App\Models\User::class,
     * @return void
     */
    protected static function setModelMapping(): void
    {
        $unparsedMapping = config('dynamic-seeder.model_mapping');
        $mapping = [];

        foreach ($unparsedMapping as $aliases => $modelClass) {
            $aliases = explode('|', $aliases);
            foreach ($aliases as $alias) {
                $mapping[$alias] = $modelClass;
            }
        }

        self::$modelMappings = $mapping;
    }


    public function getModelKey(): string
    {
        return $this->modelKey;
    }

    public function getModelAttributes(): array
    {
        return $this->attributes;
    }

    public static function getModelMapperMapping(): array
    {
        return config('dynamic-seeder.model_mapper_mapping');
    }

    public static function getDefaultModelMapping(): string
    {
        return config('dynamic-seeder.model_mapper_mapping.default');
    }

}