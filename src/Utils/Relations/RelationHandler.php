<?php
declare(strict_types=1);

namespace JsonApi\Utils\Relations;

use JsonApi\Utils\Relations\Operators\IRelationOperator;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class RelationHandler
 * @package App\JsonApi\Utils\Relations
 */
class RelationHandler
{
    /**
     * @var Model
     */
    private Model $model;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var IRelationOperator
     */
    private IRelationOperator $operator;

    /**
     * @var Model|Collection
     */
    private  $related;

    /**
     * CustomRelationOperator constructor.
     * @param Model $model
     * @param string $name
     * @param array $data
     * @param IRelationOperator $operator
     */
    public function __construct(Model $model, string $name, array $data, IRelationOperator $operator)
    {
        $this->model = $model;
        $this->name = $name;
        $this->operator = $operator;

        $this->related = $this->loadRelated($data);
    }

    /**
     * @return Model|Collection|mixed
     */
    public function getRelated()
    {
        return $this->related;
    }

    public function apply()
    {
        $this->operator->apply($this->model, $this->name, $this->related);
    }

    /**
     * @param array $data
     * @return mixed
     */
    private function loadRelated(array $data)
    {
        $this->operator->validate($data);

        $related = $this->operator->resolve($data);

        if (!$this->operator->authorize(app(Gate::class), $related)) {
            abort(403);
        }

        return $related;
    }

    /**
     * @return IRelationOperator
     */
    public function getOperator()
    {
        return $this->operator;
    }
}
