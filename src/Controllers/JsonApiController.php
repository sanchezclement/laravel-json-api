<?php
declare(strict_types=1);

namespace JsonApi\Controllers;

use Illuminate\Http\Request;
use JsonApi\Resources\ResourceBuilder;
use JsonApi\Resources\ResourceCollection;
use JsonApi\Resources\ResourceObject;
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
     * @param Request|null $request
     * @param null $query
     * @return ResourceCollection|ResourceObject|ResponseBuilder
     */
    public function response(?Request $request = null, $query = null)
    {
        if ($request) {
            return $this->builder($request)->build($query);
        } else {
            return $this->response;
        }
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
