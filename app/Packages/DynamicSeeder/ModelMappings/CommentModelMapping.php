<?php

namespace App\Packages\DynamicSeeder\ModelMappings;

use App\Packages\DynamicSeeder\Repository\ModelMappingRepository;

class CommentModelMapping extends DefaultModelMapping
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

    /**
     * {@inheritdoc}
     */
    public function inheritFromParent(): array
    {
        return [
            'post_id' => 'id',
        ];
    }
}