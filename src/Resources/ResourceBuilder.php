<?php
declare(strict_types=1);

namespace JsonApi\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonApi\Binders\JsonApiBinder;
use JsonApi\Requests\BaseRequest;
use JsonApi\Requests\DeleteRequest;
use JsonApi\Requests\IndexRequest;
use JsonApi\Requests\Params\Filter;
use JsonApi\Requests\Params\Inclusion;
use JsonApi\Requests\Params\Pagination;
use JsonApi\Requests\Params\Sorting;
use JsonApi\Requests\RequestBuilder;
use JsonApi\Requests\ResourceRequest;
use JsonApi\Responses\DeletedResponse;

/**
 * Class ResourceBuilder
 * @package JsonApi\Responses
 */
class ResourceBuilder
{
    /**
     * @var Request
     */
    private Request $request;

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
     * ResourceBuilder constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->parseRequest($request);
    }

    /**
     * @param null $builder
     * @return ResourceObject|DeletedResponse
     */
    public function build($builder = null): JsonResource
    {
        if ($this->request instanceof IndexRequest) {
            return $this->collection($builder);
        } else {
            return $this->resource();
        }
    }

    /**
     * @return JsonResponse|JsonResource
     */
    public function resource()
    {
        if ($this->request instanceof DeleteRequest) {
            return JsonApiBinder::get()->makeResource($this->model, $this->inclusion);
        } else {
            return new DeletedResponse();
        }
    }

    /**
     * @param null $builder
     * @return ResourceCollection
     */
    public function collection($builder = null): ResourceCollection
    {
        $builder = RequestBuilder::make(
            $this->filter, $this->pagination, $this->sorting
        )->build($builder ?? $this->model);

        return new ResourceCollection($builder->get(), $this->pagination, $this->inclusion);
    }

    /**
     * @param Request $request
     */
    private function parseRequest(Request $request): void
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
        }
    }
}
