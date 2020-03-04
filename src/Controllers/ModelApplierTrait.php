<?php
declare(strict_types=1);

namespace JsonApi\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JsonApi\Requests\BodyRequest;
use JsonApi\Utils\Relations\RelationHandler;

/**
 * Trait ModelApplierTrait
 * @package JsonApi\Controllers
 */
trait ModelApplierTrait
{
    /**
     * @param Model $model
     * @param BodyRequest $request
     * @param callable $beforeSave
     * @param callable $afterSave
     */
    final public function apply(
        Model $model,
        BodyRequest $request,
        ?callable $beforeSave = null,
        ?callable $afterSave = null): void
    {
        $this->applyAttributes($model, $request->getAttributes());
        $this->applyToOneRelation($model, $request);

        $beforeSave ? $beforeSave($model) : null;

        $model->save();

        $afterSave ? $afterSave($model) : null;

        $this->applyToManyRelation($model, $request);
    }

    /**
     * @param Model $model
     * @param array $attributes
     */
    final public function applyAttributes(Model $model, array $attributes): void
    {
        $model->fill(collect($attributes)->mapWithKeys(function ($value, $key) {
            return [Str::snake($key) => $value];
        })->all());
    }

    /**
     * @param Model $model
     * @param BodyRequest $request
     */
    final public function applyToOneRelation(Model $model, BodyRequest $request): void
    {
        $this->applyRelations($model, $request->getToOneRelation());
    }

    /**
     * @param Model $model
     * @param BodyRequest $request
     */
    final public function applyToManyRelation(Model $model, BodyRequest $request): void
    {
        $this->applyRelations($model, $request->getToManyRelation());
    }

    /**
     * @param Model $model
     * @param Collection $toOneRelations
     */
    final public function applyRelations(Model $model, Collection $toOneRelations): void
    {
        $toOneRelations->each(fn(RelationHandler $relation) => $relation->apply($model));
    }
}
