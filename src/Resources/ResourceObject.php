<?php
declare(strict_types=1);

namespace JsonApi\Resources;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use JsonApi\Binders\JsonApiBinder;
use JsonApi\Requests\Params\Inclusion;

/**
 * Class ResourceObject
 * @package App\JsonApi\Resources
 */
abstract class ResourceObject extends JsonResource
{
    /**
     * @var Inclusion
     */
    private Inclusion $inclusion;

    /**
     * Create a new resource instance.
     *
     * @param mixed $resource
     * @param Inclusion $inclusion
     */
    public function __construct($resource, ?Inclusion $inclusion = null)
    {
        parent::__construct($resource);

        $this->inclusion = $inclusion ?? Inclusion::make();
    }

    /**
     * @param array $parameters
     * @return ResourceObject|JsonResource
     */
    public static function make(...$parameters)
    {
        $model = array_shift($parameters);

        if (self::class === static::class) {
            return JsonApiBinder::get()->makeResource($model, ...$parameters);
        } else {
            return new static($model, ...$parameters);
        }
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    final public function toArray($request)
    {
        return [
            'type' => JsonApiBinder::get()->getName($this->resource),
            'id' => $this->resource->getKey(),
            'attributes' => $this->toAttributes($request),
            'relationships' => $this->toRelationships(),
            'links' => $this->toLinks(),
            'meta' => $this->getPolicies($this->getPolicyNames()),
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    abstract protected function toAttributes(Request $request): array;

    /**
     * @return Collection
     */
    final private function toRelationships()
    {
        return $this->inclusion->mapWithKeys(function (string $relationName) {
            return [$relationName => RelationshipObject::make($this->resource, $relationName)];
        });
    }

    /**
     * @return array
     */
    final private function toLinks(): array
    {
        return [
            'self' => route(JsonApiBinder::get()->getName($this->resource) . ".id", $this->resource->getKey())
        ];
    }

    /**
     * @param array $policyNames
     * @return array
     */
    final private function getPolicies(array $policyNames): array
    {
        $gate = app(Gate::class);

        return array_map(fn(string $policy) => $gate->check($policy, $this->resource), $policyNames);
    }

    /**
     * @return array
     */
    protected function getPolicyNames(): array
    {
        return [];
    }

    /**
     * Get any additional data that should be returned with the resource array.
     *
     * @param Request $request
     * @return array
     */
    public function with($request)
    {
        return [
            'meta' => config('json-api.meta'),
            'included' => IncludedObject::make($this->resource, $this->inclusion)->toArray(),
        ];
    }
}
