# TASK


Let's imagine you have a backend application, and you need to populate the database with "demo" data given by the business (so no random stuff). An obvious solution would be to create seeders, but you have a lot of relations that depend on each other, and you need to be able to easily maintain those seeders.

So, they must be:
1) Scalable, since the application will scale a lot over time
2) Easily maintainable
3) Human-friendly

Clearly, seeders in the SQL inserts format do not match these criteria. When I was thinking about readability, I came up with the idea that we could actually describe all data in a YAML [file](https://gist.github.com/brezzhnev/0f0071d84e4956a43ddd230bc08aa96a), and if it becomes bigger, we can split it easily.

> From your end, please think about how you would implement a logic that parses that YAML file and writes all relations/entities to the database.
An ideal format of output from you is a description of a potential solution that you think works the best and sketches of structure/classes/handlers/function calls (only high-level logic, fine to skip details).


----

# SOLUTION

```
    users
        id - integer
        name - string
        email - string
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
        
    user_details
        id - integer
        user_id - integer
        title - string
        text - string
        created_at - timestamp
        updated_at - timestamp
```


## REQUIREMENTS


- PHP **^8.0**
- MySQL **^8**


## HOW TO INSTALL


**1)** Crete copies and rename the following files:

* `.env.example` => `.env`

**2)** Edit `.env` configuration

**3)** Run command `composer install` or `php composer.phar install`

**4)** Run command `php artisan migrate` (Optional if migrations present)

**5)** Run command `npm install && npm run dev`

**6)** Run command `php artisan key:generate && php artisan config:clear`


## AFTER PULLING


Run following commands after pulling fresh commit, to be sure all db and dependency changes applied

**1)** `composer update`

**2)** `php artisan migrate && php artisan cache:clear && php artisan config:cache`

**3)** `npm update && npm run dev`

**4)** `php artisan optimize`


## DEPLOYMENT


**1)** `composer install --optimize-autoloader --no-dev`

**2)** `php artisan config:cache`

**3)** `php artisan route:cache`

**4)** `php artisan view:cache`

**5)** `npm run prod`

- **or** `composer install --optimize-autoloader --no-dev && php artisan config:cache && php artisan route:cache && php artisan view:cache && npm run prod`


## DEPENDENCIES


- [laravel/laravel](https://github.com/laravel/laravel)
- [symphony/yaml](https://github.com/symfony/yaml)