<?php
declare(strict_types=1);

namespace JsonApi\Requests\Traits;

use Illuminate\Support\Arr;

/**
 * Trait HasBody
 * @package App\JsonApi\Request\Traits
 */
trait HasBody
{
    use HasAttributes, HasRelations {
        HasAttributes::__construct as __constructHasAttributes;
        HasRelations::__construct as __constructHasRelations;
    }

    /**
     * HasBody constructor.
     */
    public function __construct()
    {
        $this->__constructHasAttributes();
        $this->__constructHasRelations();

        $this->beforeValidation(function () {
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
        });
    }
}
