<?php
declare(strict_types=1);

namespace JsonApi\Binders;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use JsonApi\Resources\ResourceObject;

/**
 * Class JsonApiBinder
 * @package JsonApi\Binders;
 */
class JsonApiBinder
{
    /**
     * @var JsonApiBinder $this
     */
    private static self $instance;

    /**
     * @var array
     */
    private array $config;

    /**
     * JsonApiBinder constructor.
     */
    private function __construct()
    {
        static::$instance = $this;

        $this->config = config('resources', ['resources' => [], 'reverse' => []]);
    }

    /**
     * @return static
     */
    public static function get(): self
    {
        return static::$instance ?? new static();
    }

    /**
     * @param $object
     * @param array $parameters
     * @return Model
     */
    public function makeModel($object, ...$parameters): Model
    {
        return $this->getModelClass($object)::make(...$parameters);
    }

    /**
     * @param mixed $object
     * @return Model
     */
    public function getModelClass($object): string
    {
        return $this->getData($object)['model'];
    }

    /**
     * @param $name
     * @return array
     */
    private function getData($name): array
    {
        if (!is_string($name) || !array_key_exists($name, $this->config['resources'])) {
            $name = $this->getName($name);
        }

        return $this->config['resources'][$name];
    }

    /**
     * @param mixed $object
     * @param bool $strict
     * @return string
     */
    public function getName($object, bool $strict = true): ?string
    {
        if (in_array($object, array_keys($this->config['resources']))) {
            return $object;
        } else {
            if (is_string($object)) {
                $class = $object;
            } else {
                $class = get_class($object);
            }

            if (!array_key_exists($class, $this->config['reverse']) && $strict) {
                abort(500, "The class '$class' has no associated resource name.");
            }

            return $this->config['reverse'][$class] ?? null;
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->config['resources']);
    }

    /**
     * @param $type
     * @param $id
     * @return Collection|Model|null
     */
    public function findOrNull($type, $id = null)
    {
        return $this->findOr($type, $id);
    }

    /**
     * @param $type
     * @param $id
     * @param null $default
     * @return Collection|Model|null
     */
    public function findOr($type, $id = null, $default = null)
    {
        $identifiers = $this->parseToIdentifier($type, $id);
        $models = $this->find($identifiers);

        if ($identifiers->has('id')) {
            $modelsNotFound = is_null($models);
        } else {
            $modelsNotFound = $models->count() !== $identifiers->count();
        }

        return $modelsNotFound ? is_callable($default) ? $default() : $default : $models;
    }

    /**
     * @param $type
     * @param null $id
     * @return \Illuminate\Support\Collection
     */
    final private function parseToIdentifier($type, $id = null)
    {
        if (!is_null($id)) {
            return collect(['type' => $type, 'id' => $id]);
        } else {
            return is_array($type) ? collect($type) : $type;
        }
    }

    /**
     * @param $type
     * @param $id
     * @return Model|Collection
     */
    public function find($type, $id = null)
    {
        return $this->findFromIdentifiers($this->parseToIdentifier($type, $id));
    }

    /**
     * @param \Illuminate\Support\Collection $identifiers
     * @return Collection|Model
     */
    private function findFromIdentifiers(\Illuminate\Support\Collection $identifiers)
    {
        if ($identifiers->has('id')) {
            return $this->getModelClass($identifiers->get('type'))::find($identifiers->get('id'));
        } else {
            return new Collection(
                collect($identifiers)
                    ->mapToGroups(fn(array $identifier) => [$identifier['type'] => $identifier['id']])
                    ->map(fn($ids, string $type) => $this->getModelClass($type)::find($ids))
                    ->collapse()->all()
            );
        }
    }

    /**
     * @param mixed $object
     * @param array $parameters
     * @return ResourceObject
     */
    public function makeResource($object, ...$parameters): ResourceObject
    {
        return $this->getResourceClass($object)::make($object, ...$parameters);
    }

    /**
     * @param mixed $object
     * @return ResourceObject|string
     */
    public function getResourceClass($object): string
    {
        return $this->getData($object)['resource'];
    }

    /**
     * @param Model $model
     * @return array
     */
    public function getIdentifier(Model $model): array
    {
        return ['type' => $this->getName($model), 'id' => $model->getKey()];
    }

    /**
     * @param $object
     * @return bool
     */
    public function isResolvable($object): bool
    {
        return $this->getName($object, false) ? true : false;
    }

    /**
     * @return array
     */
    public function getResources(): array
    {
        return $this->config['resources'];
    }
}
