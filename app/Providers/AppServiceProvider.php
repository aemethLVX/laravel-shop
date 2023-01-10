<?php

namespace App\Providers;

use Carbon\CarbonInterval;
use Faker\{Factory, Generator};
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Services\Faker\FakerImageProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Model::shouldBeStrict(!app()->isProduction());

        if (app()->isProduction()) {
            DB::listen(function ($query) {
                if ($query->time > 1000) {
                    logger()->channel('telegram')->debug(
                        "Query longer than 1s: {$query->sql} {$query->bindings}"
                    );
                }
            });

            app(Kernel::class)->whenRequestLifecycleIsLongerThan(
                CarbonInterval::seconds(4),
                function () {
                    logger()->channel('telegram')->debug(
                        'whenRequestLifecycleIsLongerThan: ' . request()->url()
                    );
                }
            );
        }

        $this->app->singleton(Generator::class, function () {
            $faker = Factory::create();
            $faker->addProvider(new FakerImageProvider($faker));
            return $faker;
        });
    }
}
