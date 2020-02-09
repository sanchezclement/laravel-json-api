<?php
declare(strict_types=1);

namespace JsonApi\Tests\Traits;

use JsonApi\Tests\JsonApiTestResponse;

/**
 * Trait StatusCodeAssertionTrait
 * @package JsonApi\Tests\Traits
 * @mixin JsonApiTestResponse
 */
trait StatusCodeAssertionTrait
{
    /**
     * Assert that the response has the given status code.
     *
     * @param int $status
     * @return JsonApiTestResponse
     */
    public function assertStatus($status)
    {
        parent::assertStatus($status);

        return $this;
    }

    /**
     * @return JsonApiTestResponse
     */
    public function assertUnprocessableEntity()
    {
        return $this->assertStatus(422);
    }
}
