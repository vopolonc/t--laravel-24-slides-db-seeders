<?php

namespace App\Packages\DynamicSeeder\ModelMappings;


class DefaultModelMapping extends DynamicSeederModelMapping
{
    protected array $attributes = [
//        'name',
//        'password',
//        'email',
    ];

    /**
     * {@inheritdoc}
     */
    public static function getAttributesToCast(): array
    {
        return [
//            'password' => fn($value) => Hash::make($value),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function relatesTo(): array
    {
        return [
//            'user' => ['user_id', fn ($value) => User::findOrFail($value)->id],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function inheritFromParent(): array
    {
        return [
//            'user_id' => 'id',
        ];
    }
}