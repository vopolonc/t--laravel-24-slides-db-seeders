<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dynamic Seeder Config
    |--------------------------------------------------------------------------
    |
    */

    'data_providers' => [
        'yaml' => \App\Packages\DynamicSeeder\DataProviders\YamlDataProvider::class,
        'json' => \App\Packages\DynamicSeeder\DataProviders\JsonDataProvider::class,
        # xml...
    ],
    'model_mapping' => [
        'user|users' => \App\Models\User::class,
        'userDetail|userDetails' => \App\Models\UserDetail::class,
        'post|posts' => \App\Models\Post::class,
        'comment|comments' => \App\Models\Comment::class,
        'like|likes' => \App\Models\Like::class,
    ],
    'model_mapper_mapping' => [
        \App\Models\User::class => \App\Packages\DynamicSeeder\ModelMappings\UserModelMapping::class,
        \App\Models\UserDetail::class => \App\Packages\DynamicSeeder\ModelMappings\UserDetailModelMapping::class,
        \App\Models\Post::class => \App\Packages\DynamicSeeder\ModelMappings\PostModelMapping::class,
        \App\Models\Comment::class => \App\Packages\DynamicSeeder\ModelMappings\CommentModelMapping::class,
        \App\Models\Like::class => \App\Packages\DynamicSeeder\ModelMappings\LikeModelMapping::class,
        'default' => \App\Packages\DynamicSeeder\ModelMappings\DefaultModelMapping::class,
    ],
];