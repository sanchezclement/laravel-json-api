<?php
declare(strict_types=1);

namespace JsonApi\Responses;

use Illuminate\Http\JsonResponse;

/**
 * Class AcceptedResponse
 * @package JsonApi\Responses
 */
class NoContentResponse extends JsonResponse
{
    /**
     * AcceptedResponse constructor.
     */
    public function __construct()
    {
        parent::__construct(null, 204, [], 0);
    }

    /**
     * @return static
     */
    public static function make(): self
    {
        return new static();
    }
}
