<?php
declare(strict_types=1);

namespace JsonApi\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;

/**
 * Class DiscoverResources
 * @package App\Console\Commands
 */
class DiscoverResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resources:discover';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Discover all the JSON API resources of the API';

    /**
     * @var array
     */
    private $config = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $modelsDir = app_path('Models');
        $resource = opendir($modelsDir);

        if (is_null($resource)) {
            $this->error("Error, cannot open $modelsDir");
            return 1;
        }

        $this->config = ['resources' => [], 'reverse' => []];
        $this->discoverModels($resource);
        $this->writeDiscoveredResources();
        return 0;
    }

    /**
     * @param resource $resource
     */
    private function discoverModels($resource): void
    {
        while (($entry = readdir($resource)) !== false) {
            $modelName = str_replace('.php', '', $entry);

            $this->discoverModel($modelName);
        }
    }

    /**
     * @param string $modelName
     */
    private function discoverModel(string $modelName): void
    {
        $modelClass = "App\Models\\$modelName";

        if (class_exists($modelClass)) {
            $this->discoverResourceData($modelClass);
        }
    }

    /**
     * @param string $modelClass
     */
    private function discoverResourceData(string $modelClass)
    {
        if (method_exists($modelClass, 'getResourceData')) {
            [$name, $resourceClass, $policyClass] = call_user_func([$modelClass, 'getResourceData']);
        } else {
            $modelNamespace = explode('\\', $modelClass);
            $modelName = end($modelNamespace);
            $name = Pluralizer::plural(Str::kebab($modelName));
            $resourceClass = "App\Http\Resources\\{$modelName}Resource";
            $policyClass = "App\Policies\\{$modelName}Policy";
        }

        if (class_exists($resourceClass) && class_exists($policyClass)) {
            $this->registerResource(
                $name,
                $modelClass,
                class_exists($resourceClass) ? $resourceClass: null,
                class_exists($policyClass) ? $policyClass : null
            );
        }
    }

    /**
     * @param string $name
     * @param string $modelClass
     * @param string $resourceClass
     * @param string $policyClass
     * @return void
     */
    private function registerResource(
        string $name,
        string $modelClass,
        ?string $resourceClass,
        ?string $policyClass): void
    {
        $this->config['reverse'][$modelClass] = $name;
        $this->config['reverse'][$resourceClass] = $name;
        $this->config['reverse'][$policyClass] = $name;

        $this->config['resources'][$name] = [
            'model' => $modelClass,
            'resource' => $resourceClass,
            'policy' => $policyClass
        ];

        $this->info("Discovered $name : $modelClass | $resourceClass | $policyClass");
    }

    private function writeDiscoveredResources(): void
    {
        $text = '<?php return ' . var_export($this->config, true) . ';';
        file_put_contents(config_path('resources.php'), $text);
    }
}
