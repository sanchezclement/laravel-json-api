<?php
declare(strict_types=1);

namespace JsonApi\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonApi\Binders\JsonApiBinder;
use JsonApi\Requests\BaseRequest;
use JsonApi\Requests\IndexRequest;
use JsonApi\Requests\Params\Filter;
use JsonApi\Requests\Params\Inclusion;
use JsonApi\Requests\Params\Pagination;
use JsonApi\Requests\Params\Sorting;
use JsonApi\Requests\RequestBuilder;
use JsonApi\Requests\ResourceRequest;

/**
 * Class ResourceBuilder
 * @package JsonApi\Responses
 */
class ResourceBuilder
{
    /**
     * @var bool
     */
    private bool $parsed = false;

    /**
     * @var bool
     */
    private bool $isIndex = false;

    /**
     * @var Model
     */
    private Model $model;

    /**
     * @var Inclusion
     */
    private Inclusion $inclusion;

    /**
     * @var Filter
     */
    private Filter $filter;

    /**
     * @var Pagination
     */
    private Pagination $pagination;

    /**
     * @var Sorting
     */
    private Sorting $sorting;

    /**
     * @param null $builder
     * @return JsonResource
     */
    public function build($builder = null): JsonResource
    {
        if (!$this->parsed) {
            abort(500, "The request has not been parsed.");
        }

        if ($this->isIndex) {
            return $this->collection($builder);
        } else {
            return $this->resource();
        }
    }

    /**
     * @return JsonResource
     */
    public function resource(): JsonResource
    {
        return JsonApiBinder::get()->makeResource($this->model, $this->inclusion);
    }

    /**
     * @param null $builder
     * @return ResourceCollection
     */
    public function collection($builder = null): ResourceCollection
    {
        $builder = RequestBuilder::make($this->filter, $this->pagination, $this->sorting)->build($builder);

        return new ResourceCollection($builder->get(), $this->pagination, $this->inclusion);
    }

    /**
     * @param Request $request
     */
    public function parseRequest(Request $request): void
    {
        if ($request instanceof BaseRequest) {
            $this->model = $request->getModel();
        }

        if ($request instanceof ResourceRequest) {
            $this->inclusion = $request->getInclusions();
        }

        if ($request instanceof IndexRequest) {
            $this->filter = $request->getFilter();
            $this->pagination = $request->getPagination();
            $this->sorting = $request->getSorting();
            $this->isIndex = true;
        }

        $this->parsed = true;
    }
}
