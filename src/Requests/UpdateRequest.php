<?php
declare(strict_types=1);

namespace App\JsonApi\Requests;

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
