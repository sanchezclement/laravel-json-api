<?php
declare(strict_types=1);

namespace JsonApi\Requests;

use Illuminate\Support\Arr;
use JsonApi\Requests\Traits\HasAttributes;
use JsonApi\Requests\Traits\HasInclusion;
use JsonApi\Requests\Traits\HasRelations;
use JsonApi\Resources\ResourceObject;

/**
 * Class BodyRequest
 * @package App\JsonApi\Requests
 */
class BodyRequest extends BaseRequest
{
    use HasInclusion, HasAttributes, HasRelations {
        HasInclusion::__construct as __constructHasInclusion;
        HasRelations::__construct as __constructHasRelations;
    }

    /**
     * @param array $parameters
     */
    public function __construct(...$parameters)
    {
        parent::__construct(...$parameters);

        $this->__constructHasInclusion();
        $this->__constructHasRelations();

        $this->beforeValidation(function () {
            $this->initializeBody();
        });
    }

    /**
     * @return ResourceObject
     */
    public function makeResource()
    {
        return ResourceObject::make($this->getModel(), $this->getInclusions());
    }

    private function initializeBody(): void
    {
        $rules = [
            'data' => 'required|array',
            'data.type' => 'required_with:data,string',
            'data.attributes' => 'array',
            'data.relationships' => 'array',
            'data.relationships.*' => 'array',
            'data.relationships.*.data' => 'required_with:data.relationships.*|array',
            'data.relationships.*.data.id' => 'required_with:data.relationships.*',
            'data.relationships.*.data.type' => 'required_with:data.relationships.*'
        ];

        foreach ($this->attributesRules() as $key => $attribute) {
            $rules['data.attributes.' . $key] = $attribute;
        }

        foreach (Arr::wrap($this->getRequiredRelations()) as $relation) {
            $rules['data.relationships.' . $relation] = 'required';
        }

        $this->addRules($rules);
    }
}
