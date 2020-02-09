<?php
declare(strict_types=1);

namespace JsonApi\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JsonApi\Requests\Interfaces\IHasModel;
use JsonApi\Requests\Traits\HasModel;

/**
 * Class BaseRequest
 * @package App\JsonApi\Requests
 */
class BaseRequest extends FormRequest implements IHasModel
{
    use HasModel;

    /**
     * @var callable[]
     */
    private array $beforeValidation = [];

    /**
     * @var callable[]
     */
    private array $afterValidation = [];

    /**
     * @var array
     */
    private array $rules = [];

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

        $this->initializeModel();
    }

    /**
     * @param callable $callback
     * @return void
     */
    public final function beforeValidation(callable $callback): void
    {
        $this->beforeValidation[] = $callback;
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
     * @return array
     */
    public final function rules(?array $rules = null): array
    {
        if ($rules) {
            $this->rules = array_merge($this->rules, $rules);
        }

        return $rules;
    }

    protected final function prepareForValidation()
    {
        foreach ($this->beforeValidation as $callback) {
            $callback();
        }
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected final function passedValidation()
    {
        foreach ($this->afterValidation as $callback) {
            $callback();
        }
    }
}
