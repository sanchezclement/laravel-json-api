<?php
declare(strict_types=1);

namespace JsonApi\Requests\Traits;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Model;
use JsonApi\Exceptions\NotImplementedFunction;

/**
 * Trait HasModel
 * @package App\JsonApi\Request\Traits
 */
trait HasModel
{
    /**
     * @var Model
     */
    private Model $model;

    public function initializeModel(): void
    {
        $this->beforeValidation(function () {
            $this->model = $this->route()->parameter('model');

            if (!$this->passesAuthorization()) {
                $this->failedAuthorization();
            }
        });
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
    private final function passesAuthorization()
    {
        return $this->authorize(app(Gate::class), $this->model) ? true : false;
    }

    /**
     * @param Gate $gate
     * @param Model $model
     * @return mixed
     * @throws NotImplementedFunction
     */
    private function authorize(Gate $gate, Model $model)
    {
        return $gate->raw($this->getPolicy(), $model->exists ? $model : get_class($model));
    }

    /**
     * @return string
     */
    private function getPolicy(): string
    {
        throw new NotImplementedFunction();
    }

    private final function failedAuthorization()
    {
        abort(403, "The main resource action is not authorized");
    }
}
