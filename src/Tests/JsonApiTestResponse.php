<?php
declare(strict_types=1);

namespace JsonApi\Tests;

use Illuminate\Foundation\Testing\Assert as PHPUnit;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

/**
 * Class JsonApiTestResponse
 * @package JsonApi\Tests
 */
class JsonApiTestResponse extends TestResponse
{
    /**
     * Create a new test response instance.
     *
     * @param Response $response
     * @return void
     */
    public function __construct($response)
    {
        parent::__construct($response);
    }

    /**
     * @param string $type
     * @return JsonApiTestResponse
     */
    public function assertType(string $type)
    {
        $this->assertJson([
            'data' => [
                'type' => $type,
            ]
        ]);

        return $this;
    }

    /**
     * @param string $type
     * @param $id
     * @return JsonApiTestResponse
     */
    public function assertIdentifier(string $type, $id)
    {
        $this->assertJson([
            'data' => [
                'type' => $type,
                'id' => $id,
            ]
        ]);

        return $this;
    }

    /**
     * @param array $attributes
     * @return JsonApiTestResponse
     */
    public function assertAttributes(array $attributes)
    {
        $this->assertJson([
            'data' => [
                'attributes' => $attributes,
            ]
        ]);

        return $this;
    }

    /**
     * @param array $attributes
     * @return JsonApiTestResponse
     */
    public function assertHasAttributes(array $attributes)
    {
        $this->assertJsonStructure([
            'data' => [
                'attributes' => $attributes,
            ]
        ]);

        return $this;
    }

    /**
     * @param array $attributes
     * @return JsonApiTestResponse
     */
    public function assertExactAttributes(array $attributes)
    {
        $actual = json_encode(Arr::get(Arr::sortRecursive(
            (array) $this->decodeResponseJson()
        ), 'data.attributes', []));

        PHPUnit::assertEquals(json_encode(Arr::sortRecursive($attributes)), $actual);

        return $this;
    }

    /**
     * @param string $id
     * @return JsonApiTestResponse
     */
    public function assertId($id)
    {
        $this->assertJson([
            'data' => [
                'id' => $id,
            ]
        ]);

        return $this;
    }

    /**
     * @return JsonApiTestResponse
     */
    public function assertHasId()
    {
        $this->assertJsonStructure([
            'data' => ['id']
        ]);

        return $this;
    }

    /**
     * @return JsonApiTestResponse
     */
    public function assertNoData()
    {
        $this->assertJson([
            'data' => null
        ]);

        return $this;
    }

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
}
