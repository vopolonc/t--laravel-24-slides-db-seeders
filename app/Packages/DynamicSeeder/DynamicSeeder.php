<?php

namespace App\Packages\DynamicSeeder;

use App\Packages\DynamicSeeder\DataProviders\DynamicSeederDataProvider;
use App\Packages\DynamicSeeder\ModelMappings\DynamicSeederModelMapping;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Exception\ParseException;

class DynamicSeeder
{
    public DynamicSeederDataProvider $provider;

    public function __construct(DynamicSeederDataProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param string $filename
     * @return bool
     * @throws \Exception|ParseException
     */
    public function seedFromFile(string $filename): bool
    {
        $this->provider->parseFile($filename);

        return $this->seed($this->provider->getData());
    }

    /**
     * @param string $input
     * @return bool
     * @throws \Exception|ParseException
     */
    public function seedFromString(string $input): bool
    {
        $this->provider->parseString($input);

        return $this->seed($this->provider->getData());
    }

    /**
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    protected function seed(array $data = []): bool
    {
        if (empty($data)) {
            return false;
        }

        DB::beginTransaction();
        try {
            foreach ($data as $alias => $datum) {
                $this->seedDatum($alias, $datum);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return true;
    }

    protected function seedDatum(string|int $alias, array $datum, ?Model $parent = null)
    {
        if (Arr::isList($datum)) {
            foreach ($datum as $data) {
                $this->seedDatum($alias, $data, $parent);
            }

            return null;
        }

        $model = DynamicSeederModelMapping::determineModel($alias);
        $mapper = DynamicSeederModelMapping::determineMapper($model::class);

        $relations = [];

        # seeding model with values
        foreach ($datum as $attributeName => $value) {

            $attributeName = Str::snake($attributeName);

            # skip if property is relation (to seed relations after model creation)
            if (is_array($value)) {
                $relations[$attributeName] = $value;
                continue;
            }

            # if property is parent (or present) relation
            if ($mapper->isRelatesTo($attributeName)) {
                [$relationAttributeName, $fn] = $mapper->relatesTo()[$attributeName];
                $model->setAttribute($relationAttributeName, $fn($value));
                continue;
            }

            # skip not listed attributes
            if (!$mapper->allowAttribute($attributeName)) {
                continue;
            }

            # cast value if needed
            $value = $mapper->castValue($attributeName, $value);

            $model->setAttribute($attributeName, $value);
        }

        # inherit parent attributes
        foreach ($mapper->inheritFromParent() as $attributeName => $parentAttributeName) {
            $value = $mapper->castValue($attributeName, $parent->{$parentAttributeName});
            $model->setAttribute($attributeName, $value);
        }

        $model->save();

        # seed nested relations marking current model as parent
        foreach ($relations as $relationAlias => $data) {
            $this->seedDatum($relationAlias, $data, $model);
        }

        return null;
    }
}