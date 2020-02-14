<?php
declare(strict_types=1);

namespace JsonApi\Responses;

use Illuminate\Http\JsonResponse;

/**
 * Class AcceptedResponse
 * @package JsonApi\Responses
 */
class DeletedResponse extends JsonResponse
{
    /**
     * AcceptedResponse constructor.
     */
    public function __construct()
    {
        parent::__construct([
            'meta' => config('json-api.top-level-meta'),
        ], 200, [], 0);
    }
}
