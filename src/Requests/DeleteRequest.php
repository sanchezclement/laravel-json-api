<?php
declare(strict_types=1);

namespace JsonApi\Requests;

use JsonApi\Resources\EmptyResourceObject;
use JsonApi\Resources\ResourceObject;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * @return EmptyResourceObject
     */
    public function makeResource()
    {
        return EmptyResourceObject::make(null);
    }
}
