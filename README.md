# t--laravel-24-slides-db-seeders

Let's imagine you have a backend application, and you need to populate the database with "demo" data given by the business (so no random stuff). An obvious solution would be to create seeders, but you have a lot of relations that depend on each other, and you need to be able to easily maintain those seeders.

So, they must be:
1) Scalable, since the application will scale a lot over time
2) Easily maintainable
3) Human-friendly

Clearly, seeders in the SQL inserts format do not match these criteria. When I was thinking about readability, I came up with the idea that we could actually describe all data in a YAML file (https://gist.github.com/brezzhnev/0f0071d84e4956a43ddd230bc08aa96a), and if it becomes bigger, we can split it easily.
From your end, please think about how you would implement a logic that parses that YAML file and writes all relations/entities to the database.
 An ideal format of output from you is a description of a potential solution that you think works the best and sketches of structure/classes/handlers/function calls (only high-level logic, fine to skip details).
