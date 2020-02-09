<?php
declare(strict_types=1);

namespace JsonApi\Requests;

use JsonApi\Requests\Interfaces\IHasInclusion;
use JsonApi\Requests\Traits\HasInclusion;

/**
 * Class ResourceRequest
 * @package App\JsonApi\Requests
 */
class ResourceRequest extends BaseRequest implements IHasInclusion
{
    use HasInclusion;

    /**
     * @param array $query The GET parameters
     * @param array $request The POST parameters
     * @param array $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array $cookies The COOKIE parameters
     * @param array $files The FILES parameters
     * @param array $server The SERVER parameters
     * @param string|resource|null $content The raw body data
     */
    public function __construct(
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        $this->initializeInclusion();
    }
}
