<?php
declare(strict_types=1);

namespace App\JsonApi\Resources;

use App\JsonApi\Models\JsonApiModel;
use App\JsonApi\Requests\Params\Inclusion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class IncludedObject
 * @package App\JsonApi
 */
class IncludedObject
{
    /**
     * @var Model|Collection Collection
     */
    private $resource;

    /**
     * @var array
     */
    private $param;

    /**
     * @var array
     */
    private $map;

    /**
     * Create a new resource instance.
     *
     * @param Model|Collection $resource
     * @param Inclusion $param
     */
    public function __construct($resource, Inclusion $param)
    {
        $this->resource = $resource instanceof Model ? collect($resource) : $resource;
        $this->param = $param;
        $this->map = [];
    }

    /**
     * @param $resource
     * @param Inclusion $param
     * @return IncludedObject
     */
    public static function make($resource, Inclusion $param)
    {
        return new static($resource, $param);
    }

    /**
     * @return Collection
     */
    public function toArray()
    {
        return $this->buildInclusionMap()->flatten()->map(function (JsonApiModel $model) {
            return ResourceObject::make($model, Inclusion::make());
        });
    }

    /**
     * @return Collection
     */
    private final function buildInclusionMap(): Collection
    {
        $this->param->each(function (string $relation) {
            $this->map[$relation] = [];

            $this->resource->each(function (JsonResource $model) use ($relation) {
                $this->addIncluded($model->getRelation($relation));
            });
        });

        return collect($this->map);
    }

    /**
     * @param $resource
     */
    private final function addIncluded($resource)
    {
        if ($resource instanceof JsonApiModel) {
            $this->addIncludedModel($resource);
        } else if ($resource instanceof Collection) {
            $this->addIncludedCollection($resource);
        } else {
            abort(500);
        }
    }

    /**
     * @param JsonApiModel $model
     */
    private final function addIncludedModel(JsonApiModel $model)
    {
        if (!array_key_exists($model->getName(), $this->map)) {
            $this->map[$model->getName()] = [];
        }

        $this->map[$model->getName()][$model->getKey()] = $model;
    }

    /**
     * @param Collection $collection
     */
    private final function addIncludedCollection(Collection $collection)
    {
        foreach ($collection as $value) {
            $this->addIncludedModel($value);
        }
    }
}
