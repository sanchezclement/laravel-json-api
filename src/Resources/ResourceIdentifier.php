<?php
declare(strict_types=1);

namespace App\JsonApi\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
    public final function toArray($request)
    {
        return [
            'type' => $this->resource->getName(),
            'id' => $this->resource->getKey(),
        ];
    }
}
