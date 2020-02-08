<?php
declare(strict_types=1);

namespace JsonApi\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JsonApi\Requests\Traits\HasModel;
use JsonApi\Resources\ResourceObject;

/**
 * Class BaseRequest
 * @package App\JsonApi\Requests
 */
class BaseRequest extends FormRequest
{
    use HasModel;

    /**
     * @var callable[]
     */
    private array $beforeValidation;

    /**
     * @var callable[]
     */
    private array $afterValidation;

    /**
     * @var array
     */
    private array $rules = [];

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
     * @return void
     */
    public final function addRules(array $rules): void
    {
        $this->rules = array_merge($this->rules, $rules);
    }

    /**
     * @return ResourceObject
     */
    public function makeResource()
    {
        return ResourceObject::make($this->getModel());
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
