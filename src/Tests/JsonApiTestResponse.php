<?php
declare(strict_types=1);

namespace JsonApi\Tests;

use Illuminate\Foundation\Testing\Assert as PHPUnit;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use JsonApi\Tests\Traits\StatusCodeAssertionTrait;

/**
 * Class JsonApiTestResponse
 * @package JsonApi\Tests
 */
class JsonApiTestResponse extends TestResponse
{
    use StatusCodeAssertionTrait;

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
     * @param $id
     * @return JsonApiTestResponse
     */
    public function assertIdentifier(string $type, $id = null)
    {
        if (is_null($id)) {
            $this->assertJson(['data' => ['type' => $type]])->assertJsonStructure(['data' => ['id']]);
        } else {
            $this->assertJson(['data' => ['type' => $type, 'id' => $id]]);
        }

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
     * @return int
     */
    public function getId(): int
    {
        return $this->decodeResponseJson()['data']['id'];
    }

    /**
     * @param array $attributes
     * @return JsonApiTestResponse
     */
    public function assertExactAttributes(array $attributes)
    {
        $actual = json_encode(Arr::get(Arr::sortRecursive(
            (array)$this->decodeResponseJson()
        ), 'data.attributes', []));

        PHPUnit::assertEquals(json_encode(Arr::sortRecursive($attributes)), $actual);

        return $this;
    }

    /**
     * @return JsonApiTestResponse
     */
    public function assertNoData()
    {
        $this->assertJsonMissing(['data'])->assertJsonStructure(['meta']);

        return $this;
    }
}
