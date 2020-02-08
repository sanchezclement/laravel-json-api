<?php
declare(strict_types=1);

namespace JsonApi\Requests\Traits;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use JsonApi\Exceptions\NotImplementedFunction;

/**
 * Trait HasModel
 * @package App\JsonApi\Request\Traits
 * @mixin FormRequest
 */
trait HasModel
{

    protected function initializeModel()
    {
        $this->model = $this->route()->parameter('model');

        if (!$this->passesAuthorization()) {
            $this->failedAuthorization();
        }
    }

    /**
     * @return Model
     */
    public final function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @return bool
     */
    protected final function passesAuthorization(): bool
    {
        return $this->authorize(app(Gate::class), $this->model) ? true : false;
    }

    /**
     * @param Gate $gate
     * @param Model $model
     * @return mixed
     * @throws NotImplementedFunction
     */
    protected function authorize(Gate $gate, Model $model)
    {
        return $gate->raw($this->getPolicy(), $model->exists ? $model : get_class($model));
    }

    /**
     * @return string
     */
    protected function getPolicy(): string
    {
        throw new NotImplementedFunction();
    }

    protected final function failedAuthorization()
    {
        abort(403, "The main resource action is not authorized");
    }
}
