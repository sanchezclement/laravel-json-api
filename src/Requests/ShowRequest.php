<?php
declare(strict_types=1);

namespace JsonApi\Requests;

use JsonApi\Requests\Traits\HasInclusion;
use JsonApi\Resources\ResourceObject;

/**
 * Class ShowRequest
 * @package App\JsonApi\Requests
 */
class ShowRequest extends BaseRequest
{
    use HasInclusion;

    /**
     * @return string
     */
    protected function getPolicy(): string
    {
        return 'show';
    }

    /**
     * @return ResourceObject
     */
    public function makeResource()
    {
        return ResourceObject::make($this->getModel(), $this->getInclusions());
    }
}
