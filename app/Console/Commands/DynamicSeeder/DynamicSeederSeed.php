<?php

namespace App\Console\Commands\DynamicSeeder;

use App\Packages\DynamicSeeder\DynamicSeeder;
use App\Packages\DynamicSeeder\DynamicSeederProvidersFactory;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DynamicSeederSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dynamic-seeder:seed 
                            {file_name : The file name (with path) which data within going to be populated to database tables}
                            {--T|truncate : Whether the seeded tables should be truncated}
                            {data_provider=yaml : The data provider listed in config/dynamic-seeder -> data_providers, which provides the data from the file as an PHP array}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '
    Console command that populates data from a specific file (with specific extension) to a specific database tables.
    ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('truncate')) {
            foreach (config('dynamic-seeder.model_mapping') as $modelClass) {
                /** @var Model $model */
                $model = (new $modelClass());
                DB::table($model->getTable())->truncate();
            }
        }

        $dataProviderAlias = $this->argument('data_provider');
        $providerClass = config('dynamic-seeder.data_providers')[$dataProviderAlias] ?? null;
        if (!$providerClass) {
            $this->error(sprintf('Given data provider [%s] not found in config.dynamic-seeder.data-providers', $dataProviderAlias));
            return Command::FAILURE;
        }

        $factory = new DynamicSeederProvidersFactory($providerClass);
        $service = new DynamicSeeder($factory->getProvider());

        $filename = base_path($this->argument('file_name'));
        $service->seedFromFile($filename);

        return Command::SUCCESS;
    }
}
