<?php
declare(strict_types=1);

namespace JsonApi\Requests\Traits;

use JsonApi\Exceptions\NotImplementedFunction;
use JsonApi\Models\JsonApiModel;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait HasModel
 * @package App\JsonApi\Request\Traits
 */
trait HasModel
{
    /**
     * @var JsonApiModel
     */
    private $modelClass;

    /**
     * @var Model
     */
    private $model;

    /**
     * @return Model
     */
    public final function getModel()
    {
        return $this->model;
    }

    private final function initializeModel()
    {
        $this->loadModel();

        if (!$this->passesAuthorization()) {
            $this->failedAuthorization();
        }
    }

    private final function loadModel()
    {
        $name = $this->route('resource') ?? explode('/', $this->path())[0];
        $this->modelClass = JsonApiModel::getClassFromName($name);

        if (is_null($id = $this->route('id'))) {
            $this->model = $this->modelClass::make();
        } else {
            $this->model = $this->modelClass::findOrFail($id);
        }
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
        abort(403, 'HasModel::failedAuthorization');
    }
}
