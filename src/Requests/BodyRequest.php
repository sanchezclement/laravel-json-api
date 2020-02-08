<?php
declare(strict_types=1);

namespace JsonApi\Requests;

use JsonApi\Requests\Traits\HasAttributes;
use JsonApi\Requests\Traits\HasRelations;
use Illuminate\Support\Arr;

/**
 * Class BodyRequest
 * @package App\JsonApi\Requests
 */
class BodyRequest extends BaseRequest
{
    use HasAttributes, HasRelations {
        HasRelations::__construct as __constructHasRelations;
    }

    /**
     * @param array $query The GET parameters
     * @param array $request The POST parameters
     * @param array $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array $cookies The COOKIE parameters
     * @param array $files The FILES parameters
     * @param array $server The SERVER parameters
     * @param string|resource|null $content The raw body data
     */
    public function __construct(
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        $this->__constructHasRelations();

        $this->initializeBodyRequest();
    }

    /**
     * @return void
     */
    public function initializeBodyRequest(): void
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

    /**
     * @param array $parameters
     * @return void
     */
    protected function preAction(...$parameters): void
    {
        $this->applyAttributes();
        $this->applyToOneRelation();
    }

    /**
     * @param array $parameters
     */
    protected function defaultAction(...$parameters): void
    {
        $this->getModel()->save();

        $this->applyToOneRelation();
    }
}
