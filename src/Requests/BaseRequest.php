<?php
declare(strict_types=1);

namespace App\JsonApi\Requests;

use App\JsonApi\Requests\Traits\HasInclusion;
use App\JsonApi\Requests\Traits\HasModel;
use App\JsonApi\Resources\ResourceObject;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class BaseRequest
 * @package App\JsonApi\Requests
 */
class BaseRequest extends FormRequest
{
    use HasModel, HasInclusion {
        HasInclusion::__construct as __constructHasInclusion;
    }

    /**
     * @var callable[]
     */
    private $afterValidation;

    /**
     * @var array
     */
    private $rules = [];

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

        $this->__constructHasInclusion();
    }

    public function validateResolved()
    {
        $this->prepareForValidation();

        $this->initializeModel();

        $instance = $this->getValidatorInstance();

        if ($instance->fails()) {
            $this->failedValidation($instance);
        }

        $this->passedValidation();
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        foreach ($this->afterValidation as $callback) {
            $callback();
        }
    }

    /**
     * @return array
     */
    public final function rules(): array
    {
        return $this->rules;
    }

    /**
     * @param callable $callback
     * @return void
     */
    public final function afterValidation(callable $callback): void
    {
        $this->afterValidation[] = $callback;
    }

    /**
     * @param array $rules
     * @return void
     */
    public final function addRules(array $rules): void
    {
        $this->rules = array_merge($this->rules, $rules);
    }

    /**
     * @param array $parameters
     * @return ResourceObject
     */
    public final function process(...$parameters)
    {
        $this->preAction(...$parameters);

        if (method_exists($this, 'action')) {
            array_unshift($parameters, $this->getModel());
            /** @noinspection PhpUndefinedCallbackInspection */
            call_user_func([$this, 'action'], ...$parameters);
        } else {
            $this->defaultAction(...$parameters);
        }

        return $this->makeResource();
    }

    /**
     * @param array $parameters
     * @return void
     */
    protected function preAction(...$parameters): void
    {
    }

    /**
     * @param array $parameters
     * @return void
     */
    protected function defaultAction(...$parameters): void
    {
    }

    /**
     * @return ResourceObject
     */
    public function makeResource()
    {
        return ResourceObject::make($this->getModel(), $this->getInclusions());
    }
}
