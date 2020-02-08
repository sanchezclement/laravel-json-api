<?php
declare(strict_types=1);

namespace JsonApi\Resources;

use App\Http\Resources\ArticleResource;
use App\Http\Resources\CriterionResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\NegociationResource;
use App\Http\Resources\OptionResource;
use App\Http\Resources\SaleResource;
use JsonApi\Requests\Params\Inclusion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class EmptyResourceObject
 * @package App\JsonApi\Resources
 */
class EmptyResourceObject extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public final function toArray($request)
    {
        return [];
    }

    /**
     * Get any additional data that should be returned with the resource array.
     *
     * @param Request $request
     * @return array
     */
    public function with($request)
    {
        return [
            'meta' => config('json-api.top-level-meta'),
        ];
    }
}
