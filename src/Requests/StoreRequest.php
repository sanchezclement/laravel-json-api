<?php
declare(strict_types=1);

namespace App\JsonApi\Requests;

/**
 * Class StoreRequest
 * @package App\JsonApi\Requests
 */
class StoreRequest extends BodyRequest
{
    /**
     * @return string
     */
    protected function getPolicy(): string
    {
        return 'store';
    }
}
