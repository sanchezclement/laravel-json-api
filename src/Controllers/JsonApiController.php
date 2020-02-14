<?php
declare(strict_types=1);

namespace JsonApi\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonApi\Resources\ResourceBuilder;
use JsonApi\Responses\ResponseBuilder;

/**
 * Class JsonApiController
 * @package JsonApi\Controllers
 */
class JsonApiController
{
    /**
     * @var ResponseBuilder
     */
    private ResponseBuilder $response;

    public function __construct()
    {
        $this->response = new ResponseBuilder;
    }

    /**
     * @return ResponseBuilder
     */
    public function response(): ResponseBuilder
    {
        return $this->response;
    }

    /**
     * @param Request $request
     * @param null $builder
     * @return JsonResource
     */
    public function resource(Request $request, $builder = null): JsonResource
    {
        return (new ResourceBuilder($request))->build($builder);
    }

    /**
     * @param Request $request
     * @return ResourceBuilder
     */
    public function builder(Request $request): ResourceBuilder
    {
        return (new ResourceBuilder($request));
    }
}
