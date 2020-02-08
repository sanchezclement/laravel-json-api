<?php
declare(strict_types=1);

namespace JsonApi\Providers;

use Carbon\Laravel\ServiceProvider;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Gate;
use JsonApi\Console\Commands\DiscoverResources;

/**
 * Class JsonApiProvider
 * @package JsonApi\Providers
 */
class JsonApiProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config' => config_path(),
            ], 'laravel-json-api-config');

            $this->commands([
                DiscoverResources::class
            ]);
        }

        $this->registerPolicies();
    }

    /**
     * Register the application's policies.
     *
     * @return void
     */
    public function registerPolicies()
    {
        foreach (config('resource.modelToPolicy') as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Resource::withoutWrapping();
    }
}