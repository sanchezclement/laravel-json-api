<?php
declare(strict_types=1);

namespace App\JsonApi\Resources;

use App\JsonApi\Requests\IndexRequest;
use App\JsonApi\Requests\Params\Inclusion;
use App\JsonApi\Requests\Params\Pagination;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection as JsonResourceCollection;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;

/**
 * Class ResourceCollection
 * @package App\JsonApi\Resources
 */
class ResourceCollection extends JsonResourceCollection
{
    /**
     * @var IndexRequest
     */
    protected $pagination;

    /**
     * @var Inclusion
     */
    protected $inclusion;

    /**
     * Create a new resource instance.
     *
     * @param Collection $collection
     * @param Pagination $pagination
     * @param Inclusion $inclusion
     */
    public function __construct(Collection $collection, Pagination $pagination, Inclusion $inclusion)
    {
        $inclusion->loadMissing($collection);
        $this->pagination = $pagination;
        $this->inclusion = $inclusion;

        if ($collection->isEmpty()) {
            $this->collects = null;
        } else {
            $this->collects = ResourceObject::getClassFromName($collection[0]->getName());
        }

        parent::__construct($collection);
    }

    /**
     * Map the given collection resource into its individual resources.
     *
     * @param  mixed  $resource
     * @return mixed
     */
    protected function collectResource($resource)
    {
        if ($resource instanceof MissingValue) {
            return $resource;
        }

        $collects = $this->collects();

        $this->collection = $collects && ! $resource->first() instanceof $collects
            ? $resource->map(function ($model) use ($collects) {
                return new $collects($model, $this->inclusion);
            })
            : $resource->toBase();

        return $resource instanceof AbstractPaginator
            ? $resource->setCollection($this->collection)
            : $this->collection;
    }

    /**
     * Create a new resource instance.
     *
     * @param Collection $collection
     * @param Pagination $pagination
     * @param Inclusion $inclusion
     * @return ResourceCollection
     */
    public static function makeFromCollection(Collection $collection, Pagination $pagination, Inclusion $inclusion)
    {
        return new static($collection, $pagination, $inclusion);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public final function toArray($request)
    {
        return [
            'data' => $this->collection,
            'links' => $this->pagination->getLinks(),
            'meta' =>  [
                $this->merge(config('app.meta')),
                $this->merge($this->pagination->getMeta())
            ],
            'included' => IncludedObject::make($this->collection, $this->inclusion)->toArray()
        ];
    }
}
