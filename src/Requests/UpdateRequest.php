<?php
declare(strict_types=1);

namespace JsonApi\Requests;

/**
 * Class UpdateRequest
 * @package App\JsonApi\Requests
 */
class UpdateRequest extends BodyRequest
{
    /**
     * @return string
     */
    protected function getPolicy(): string
    {
        return 'update';
    }
}
