<?php

namespace App\Packages\DynamicSeeder\ModelMappings;

use App\Packages\DynamicSeeder\Repository\ModelMappingRepository;

class PostModelMapping extends DefaultModelMapping
{
    /**
     * {@inheritdoc}
     */
    public function relatesTo(): array
    {
        return [
            'user' => ['user_id', ModelMappingRepository::userRelationByAttribute('email')],
        ];
    }
}