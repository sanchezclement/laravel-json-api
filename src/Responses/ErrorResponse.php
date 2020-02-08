<?php
declare(strict_types=1);

namespace JsonApi\Responses;

use Illuminate\Http\JsonResponse;

/**
 * Class ErrorResponse
 * @package JsonApi\Responses
 */
class ErrorResponse extends JsonResponse
{
    /**
     * AcceptedResponse constructor.
     * @param int $statusCode
     */
    public function __construct(int $statusCode = 500)
    {
        parent::__construct(null, $statusCode, [], 0);

        $this->setData($this->toArray());
    }

    /**
     * @return array
     */
    public final function toArray()
    {
        return [
            'id' => null,
            'links' => [
                'about' => null,
            ],
            'status' => $this->getStatusCode(),
            'code' => null,
            'title' => null,
            'detail' => null,
            'source' => null,
            'meta' => null,
        ];
    }
}
