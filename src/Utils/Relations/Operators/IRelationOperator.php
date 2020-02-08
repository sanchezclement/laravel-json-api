<?php
declare(strict_types=1);

namespace JsonApi\Utils\Relations\Operators;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface IRelationOperator
 * @package App\JsonApi\Utils\Relations\Operators
 */
interface IRelationOperator
{
    /**
     * @param array $data
     * @return void
     */
    public function validate(array $data): void;

    /**
     * @param array $data
     * @return mixed
     */
    public function resolve(array $data);

    /**
     * @param Gate $gate
     * @param mixed $data
     * @return bool
     */
    public function authorize(Gate $gate, $data): bool;

    /**
     * @param Model $model
     * @param string $name
     * @param $related
     * @return void
     */
    public function apply(Model $model, string $name, $related): void;
}
