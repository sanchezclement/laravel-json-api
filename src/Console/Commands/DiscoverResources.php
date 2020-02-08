<?php
declare(strict_types=1);

namespace JsonApi\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;

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
     * @var array
     */
    private $resources = [];

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
            $name = Pluralizer::plural(strtolower($modelName));
            $resourceClass = "App\Http\Resources\\{$modelName}Resource";
            $policyClass = "App\Policies\\{$modelName}Policy";
        }
        $this->info("$name $modelClass $resourceClass $policyClass");

        if (class_exists($resourceClass) && class_exists($policyClass)) {
            $this->registerResource($name, $modelClass, $resourceClass, $policyClass);
        }
    }

    /**
     * @param string $name
     * @param string $modelClass
     * @param string $resourceClass
     * @param string $policyClass
     * @return void
     */
    private function registerResource(string $name, string $modelClass, string $resourceClass, string $policyClass): void
    {
        $this->resources[$name] = [$modelClass, $resourceClass, $policyClass];
    }

    private function writeDiscoveredResources(): void
    {
        $this->config = config('resources');

        $this->addDiscoveredResourcesToConfig();

        $text = '<?php return ' . var_export($this->config, true) . ';';
        $this->info($text);
        file_put_contents(config_path('resources.php'), $text);
    }

    private function addDiscoveredResourcesToConfig(): void
    {
        $this->config['nameToModel'] = [];
        $this->config['nameToResource'] = [];
        $this->config['modelToName'] = [];
        $this->config['modelToPolicy'] = [];

        foreach ($this->resources as $name => [$model, $resource, $policy]) {
            $this->config['nameToModel'][$name] = $model;
            $this->config['nameToResource'][$name] = $resource;
            $this->config['modelToName'][$model] = $name;
            $this->config['modelToPolicy'][$model] = $policy;
        }
    }
}
