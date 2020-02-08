<?php
declare(strict_types=1);

namespace JsonApi\Requests;

use JsonApi\Requests\Traits\HasAttributes;
use JsonApi\Requests\Traits\HasBody;
use JsonApi\Requests\Traits\HasInclusion;
use JsonApi\Requests\Traits\HasRelations;
use Illuminate\Support\Arr;
use JsonApi\Resources\ResourceObject;

/**
 * Class BodyRequest
 * @package App\JsonApi\Requests
 */
class BodyRequest extends BaseRequest
{
    use HasBody, HasInclusion;

    /**
     * @return ResourceObject
     */
    public function makeResource()
    {
        return ResourceObject::make($this->getModel(), $this->getInclusions());
    }
}
