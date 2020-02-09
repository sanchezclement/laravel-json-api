<?php
declare(strict_types=1);

namespace JsonApi\Controllers;

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

    /**
     * @var ResourceBuilder
     */
    private ResourceBuilder $resource;

    public function __construct()
    {
        $this->response = new ResponseBuilder;
        $this->resource = new ResourceBuilder;
    }

    /**
     * @return ResponseBuilder
     */
    public function response(): ResponseBuilder
    {
        return $this->response;
    }

    /**
     * @return ResourceBuilder
     */
    public function resource(): ResourceBuilder
    {
        return $this->resource;
    }
}
