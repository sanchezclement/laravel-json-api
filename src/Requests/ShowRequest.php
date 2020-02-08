<?php
declare(strict_types=1);

namespace JsonApi\Requests;

/**
 * Class ShowRequest
 * @package App\JsonApi\Requests
 */
class ShowRequest extends BaseRequest
{
    /**
     * @return string
     */
    protected function getPolicy(): string
    {
        return 'show';
    }
}
