<?php
declare(strict_types=1);

namespace JsonApi\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonApi\Binders\JsonApiBinder;

/**
 * Class ResourceIdentifier
 * @package App\JsonApi\Resources
 */
class ResourceIdentifier extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    final public function toArray($request)
    {
        return [
            'type' => JsonApiBinder::get()->getName($this->resource),
            'id' => $this->resource->getKey(),
        ];
    }
}
