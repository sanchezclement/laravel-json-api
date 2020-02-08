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
    public function assertSuccess()
    {
        return $this->assertStatus(200);
    }

    /**
     * @return JsonApiTestResponse
     */
    public function assertCreated()
    {
        return $this->assertStatus(201);
    }

    /**
     * @return JsonApiTestResponse
     */
    public function assertAccepted()
    {
        return $this->assertStatus(202);
    }

    /**
     * @return JsonApiTestResponse
     */
    public function assertUnauthorized()
    {
        return $this->assertStatus(401);
    }

    /**
     * @return JsonApiTestResponse
     */
    public function assertForbidden()
    {
        return $this->assertStatus(403);
    }

    /**
     * @return JsonApiTestResponse
     */
    public function assertNotFound()
    {
        return $this->assertStatus(404);
    }

    /**
     * @return JsonApiTestResponse
     */
    public function assertUnprocessableEntity()
    {
        return $this->assertStatus(422);
    }
}
