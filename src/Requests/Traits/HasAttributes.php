<?php
declare(strict_types=1);

namespace JsonApi\Requests\Traits;

use Illuminate\Support\Arr;
use JsonApi\Requests\BodyRequest;

/**
 * Trait HasAttributes
 * @package App\JsonApi\Request\Traits
 * @mixin BodyRequest
 */
trait HasAttributes
{
    /**
     * @param string $key
     * @return mixed
     */
    public function hasAttribute(string $key)
    {
        return $this->has("data.attributes.$key");
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return Arr::get($this->validated(), 'data.attributes', []);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getAttribute(string $key)
    {
        return $this->input("data.attributes.$key");
    }

    /**
     * @return array
     */
    protected function attributesRules(): array
    {
        return [];
    }
}
