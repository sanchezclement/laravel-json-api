<?php
declare(strict_types=1);

namespace JsonApi\Tests\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase;
use JsonApi\Binders\JsonApiBinder;
use JsonApi\Tests\JsonApiTestResponse;

/**
 * Trait HasJsonApiCalls
 * @package JsonApi\Tests\Traits
 * @mixin TestCase
 */
trait HasJsonApiCalls
{
    /**
     * Visit the given URI with a GET request.
     *
     * @param string $uri
     * @param array $headers
     * @return JsonApiTestResponse
     */
    public function jsonApiGet($uri, array $headers = [])
    {
        return new JsonApiTestResponse(parent::get($uri, $headers)->baseResponse);
    }

    /**
     * Visit the given URI with a POST request.
     *
     * @param string $uri
     * @param array $attributes
     * @param array $relationships
     * @param array $headers
     * @return JsonApiTestResponse
     */
    public function jsonApiPost(
        string $uri,
        array $attributes = [],
        array $relationships = [],
        array $headers = [])
    {
        return new JsonApiTestResponse(parent::json(
            'POST', $uri, $this->getJsonApiData($uri, $attributes, $relationships), $headers
        )->baseResponse);
    }

    /**
     * Visit the given URI with a PUT request.
     *
     * @param string $uri
     * @param array $attributes
     * @param array $relationships
     * @param array $headers
     * @return JsonApiTestResponse
     */
    public function jsonApiPut($uri, array $attributes, array $relationships, array $headers = [])
    {
        return new JsonApiTestResponse(
            parent::put($uri, $this->getJsonApiData($uri, $attributes, $relationships), $headers)->baseResponse
        );
    }

    /**
     * Visit the given URI with a PATCH request.
     *
     * @param string $uri
     * @param array $attributes
     * @param array $relationships
     * @param array $headers
     * @return JsonApiTestResponse
     */
    public function jsonApiPatch($uri, array $attributes = [], array $relationships = [], array $headers = [])
    {
        return new JsonApiTestResponse(
            parent::patch($uri, $this->getJsonApiData($uri, $attributes, $relationships), $headers)->baseResponse
        );
    }

    /**
     * Visit the given URI with a DELETE request.
     *
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return JsonApiTestResponse
     */
    public function jsonApiDelete($uri, array $data = [], array $headers = [])
    {
        return new JsonApiTestResponse(parent::delete($uri, $data, $headers)->baseResponse);
    }

    /**
     * @param $uri
     * @param $attributes
     * @param $relationships
     * @return array
     */
    private function getJsonApiData($uri, $attributes, $relationships)
    {
        return [
            'data' => [
                'type' => $this->getType($uri),
                'attributes' => $attributes,
                'relationships' => $this->getRelationshipsData($relationships)
            ],
        ];
    }

    /**
     * @param string $uri
     * @return mixed
     */
    private function getType(string $uri)
    {
        return explode('/', $uri)[0];
    }

    /**
     * @param array $relationships
     * @return array
     */
    private function getRelationshipsData(array $relationships)
    {
        return collect($relationships)->map(function ($related) {
            if (is_array($related)) {
                return collect($related)->map(function (Model $model) {
                    return ['data' => JsonApiBinder::get()->getIdentifier($model)];
                })->all();
            } else if (is_string($related)) {
                return ['data' => ['type' => $related, 'id' => 0]];
            } else if ($related instanceof Model) {
                return ['data' => JsonApiBinder::get()->getIdentifier($related)];
            } else {
                abort(500, "Wrong relationship data");
                return null;
            }
        })->all();
    }
}
