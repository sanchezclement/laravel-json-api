<?php
declare(strict_types=1);

namespace App\JsonApi\Requests\Traits;

use App\JsonApi\Requests\BodyRequest;

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
     * @return array
     */
    protected function attributesRules(): array
    {
        return [];
    }
}
