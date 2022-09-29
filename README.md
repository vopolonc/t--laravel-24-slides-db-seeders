# TASK

Let's imagine you have a backend application, and you need to populate the database with "demo" data given by the
business (so no random stuff). An obvious solution would be to create seeders, but you have a lot of relations that
depend on each other, and you need to be able to easily maintain those seeders.

So, they must be:

1) Scalable, since the application will scale a lot over time
2) Easily maintainable
3) Human-friendly

Clearly, seeders in the SQL inserts format do not match these criteria. When I was thinking about readability, I came up
with the idea that we could actually describe all data in a
YAML [file](https://gist.github.com/brezzhnev/0f0071d84e4956a43ddd230bc08aa96a), and if it becomes bigger, we can split
it easily.

> From your end, please think about how you would implement a logic that parses that YAML file and writes all relations/entities to the database. An ideal format of output from you is a description of a potential solution that you think works the best and sketches of structure/classes/handlers/function calls (only high-level logic, fine to skip details).


----

## SOLUTION

#### Brief Approach

> A brief approach will be considered here, you can see the code in more detail in the open pull request [implementation](https://github.com/vopolonc/t--laravel-24-slides-db-seeders/pull/1)

The first step is we need to parse the **file** into an array, format doesn't matter yaml/json/xml because as a result we always want to get a regular array with nested data.
Therefore, let's start by creating several providers and get our coveted data array.

For example yaml data provider will look something like this:

```php
class YamlDataProvider extends DynamicSeederDataProvider
{

    protected Yaml $component;
    protected int $flags = 0;

    /**
     * @throws \Exception
     */
    public function __construct(array $config = [])
    {
        $this->component = new Yaml();
        $this->populateConfig($config);
    }

    /**
     * @param string $filename
     * @return $this
     * @throws ParseException If the YAML is not valid
     */
    public function parseFile(string $filename): static
    {
        $data = Yaml::parseFile($filename, $this->flags);
        $this->setData($data);

        return $this;
    }

    /**
     * @param string $input
     * @return $this
     * @throws ParseException If the YAML is not valid
     */
    public function parseString(string $input): static
    {
        $data = Yaml::parse($input, $this->flags);
        $this->setData($data);

        return $this;
    }
}
```

Same for json provider [JsonDataProvider.php](app/Packages/DynamicSeeder/DataProviders/JsonDataProvider.php)

Put it in the config so that in the future it will not be lost and be able to select it from providers.

```php

    'data_providers' => [
        'yaml' => \App\Packages\DynamicSeeder\DataProviders\YamlDataProvider::class,
        'json' => \App\Packages\DynamicSeeder\DataProviders\JsonDataProvider::class,
        # xml...
    ],
```

Having an array of data with fields and dependencies, we need to explicitly define which models can be filled in this way. We will do this with the help of a trait:

example: [User.php](app/Models/User.php)
```php

...

class User extends Model
{
>>>> use HasDynamicSeeder;

    ...
}


```

Next, we need to make a mapping for each such model so that we can determine the dependencies and how to resolve them.

example: [DynamicSeederModelMapping.php](app/Packages/DynamicSeeder/ModelMappings/DynamicSeederModelMapping.php)
```php

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
```

Now, understanding which models we will fill in, and what aliases they will have in the array (data), we can freely write directly the mappers themselves:

example: [LikeModelMapping.php](app/Packages/DynamicSeeder/ModelMappings/LikeModelMapping.php)
```php

class LikeModelMapping extends DefaultModelMapping
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
            'comment_id' => 'id',
        ];
    }
}

```

We can also determine which fields we collect and which to ignore, and which final format (casts) will be written to the database (model):

```php
class ExampleDataMapping extends DynamicSeederModelMapping
{
    protected array $attributes = [
      'name',  
      'email',  
      'password',  
    ];
    
    /**
     * {@inheritdoc}
     */
    public static function getAttributesToCast(): array
    {
        return [
            'password' => fn($value) => Hash::make($value),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function relatesTo(): array
    {
        return [
            'user' => ['user_id', fn ($value) => User::findOrFail($value)->id],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function inheritFromParent(): array
    {
        return [
            'user_id' => 'id',
        ];
    }
}
```

We need to explicitly (in config) specify which alias in the file (data) of which model will respond, and which data mapper of which model will respond

example: [dynamic-seeder.php](config/dynamic-seeder.php)

```php
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
```

The logic of the service itself that fills the models can be viewed [here](app/Packages/DynamicSeeder/DynamicSeeder.php). 
It is worth noting that the passage through the nested array occurs by the recursion method, which is not entirely good, but if you are careful and do not exceed the nesting of dependencies more than 255, then everything will be fine.

#### Testing

For testing, we will rely on several simple tables with different nesting:

```
    users
        id - integer
        name - string
        email - string
        created_at - timestamp
        updated_at - timestamp

    user_details
        id - integer
        user_id - integer
        title - string
        text - string
        created_at - timestamp
        updated_at - timestamp

    posts
        id - integer
        user_id - integer
        title - string
        text - string
        created_at - timestamp
        updated_at - timestamp
        
    comments
        id - integer
        user_id - integer
        title - string
        text - string
        created_at - timestamp
        updated_at - timestamp
        
    likes
        id - integer
        comment_id - integer
        user_id - string
        created_at - timestamp
```

In folder `storage/test_data` there are two subfolders for json/yaml tests, you can run them one by one and see what happens in the tables. 
Using a simple command to run our tests [DynamicSeederSeed.php](app/Console/Commands/DynamicSeeder/DynamicSeederSeed.php):

- `php artisan dynamic-seeder:seed storage\test_data\yaml\test_2.yaml -T` (-T means to truncate tables)
- or for json `php artisan dynamic-seeder:seed storage\test_data\json\test_2.json json -T` (you can also specify the format of the input file)


#### Conclusions

A very interesting task that can be developed in different directions/ways. 

What could be improved in the future:

- Support for morph relations
- Data populating from multiple files
- Full description of all methods and removal of code from the god class (The task was completed in one day, so there was simply no time to fully describe all the methods)
- Ability to upload files and populate data of this format via api

What in my opinion is well done in this task:

- Possibility to add new data providers
- The ability to explicitly specify classes and their mapping to models, without clogging the model
- Ability to cast data before adding it to the model

----

## REQUIREMENTS

- PHP **^8.0**
- MySQL **^8**

## HOW TO INSTALL

**1)** Crete copies and rename the following files:

* `.env.example` => `.env`

**2)** Edit `.env` configuration

**3)** Run command `composer install` or `php composer.phar install`

**4)** Run command `php artisan migrate` (Optional if migrations present)

**6)** Run command `php artisan key:generate && php artisan config:clear`

## DEPENDENCIES

- [laravel/laravel](https://github.com/laravel/laravel)
- [symphony/yaml](https://github.com/symfony/yaml)