<?php
declare(strict_types=1);

namespace JsonApi\Requests\Traits;

use JsonApi\Requests\BodyRequest;

/**
 * Trait HasAttributes
 * @package App\JsonApi\Request\Traits
 * @mixin BodyRequest
 */
trait HasAttributes
{
    public function applyAttributes(): void
    {
        $this->getModel()->fill($this->getAttributes());
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->validated()['data']['attributes'];
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function hasAttribute(string $key)
    {
        return $this->has("data.attributes.$key");
    }

    /**
     * @return array
     */
    protected function attributesRules(): array
    {
        return [];
    }
}
