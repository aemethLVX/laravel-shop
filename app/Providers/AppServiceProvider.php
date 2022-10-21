<?php

namespace App\Providers;

use Carbon\CarbonInterval;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Model::preventLazyLoading(!app()->isProduction());
        Model::preventSilentlyDiscardingAttributes(!app()->isProduction());

        if (!app()->isProduction()) {
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
    }
}
