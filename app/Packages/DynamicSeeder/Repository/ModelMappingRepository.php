<?php

namespace App\Packages\DynamicSeeder\Repository;

use App\Models\User;

class ModelMappingRepository
{
    /**
     * @param string $attributeName
     * @return \Closure
     */
    public static function userRelationByAttribute(string $attributeName): \Closure
    {
        return function (mixed $value) use ($attributeName) {
            $user = User::select(['id', 'name'])
                ->where($attributeName, $value)
                ->first();

            return $user->id;
        };
    }
}