<?php
declare(strict_types=1);

namespace JsonApi\Requests;

/**
 * Class BaseRequest
 * @package App\JsonApi\Requests
 */
class DeleteRequest extends BaseRequest
{
    /**
     * @return string
     */
    protected function getPolicy(): string
    {
        return 'delete';
    }
}
