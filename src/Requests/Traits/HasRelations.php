<?php
declare(strict_types=1);

namespace JsonApi\Requests\Traits;

use JsonApi\Requests\BodyRequest;
use JsonApi\Utils\Relations\Operators\IRelationOperator;
use JsonApi\Utils\Relations\Operators\ToManyRelationOperator;
use JsonApi\Utils\Relations\Operators\ToOneRelationOperator;
use JsonApi\Utils\Relations\RelationHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * Trait HasRelations
 * @package App\JsonApi\Request\Traits
 * @mixin BodyRequest
 */
trait HasRelations
{
    /**
     * @var Collection|RelationHandler[]
     */
    private $relations;

    /**
     * @var array
     */
    private $policies;

    /**
     * HasRelations constructor.
     */
    public function __construct()
    {
        $this->relations = collect();
        $this->policies = $this->getRelationPolicies();

        $this->afterValidation(function () {
            foreach ($this->input('data.relationships', []) as $name => ['data' => $data]) {

                $this->relations[$name] = new RelationHandler($this->getModel(), $name, $data, $this->getOperator($name));
            }

            if (($author = $this->getAuthorRelation())) {
                $this->getModel()->{$author}()->associate($this->user());
            }
        });
    }

    public final function applyToOneRelation(): void
    {
        $this->relations->filter(function (RelationHandler $handler) {
            return $handler->getOperator() instanceof ToOneRelationOperator;
        })->each(function (RelationHandler $handler) {
            $handler->apply();
        });
    }

    /**
     * @param string $name
     * @return Model|Collection|mixed
     */
    public final function getRelation(string $name)
    {
        return $this->relations[$name]->getRelated();
    }

    public final function applyToManyRelation(): void
    {
        $this->relations->where(function (RelationHandler $handler) {
            return $handler->getOperator() instanceof ToManyRelationOperator;
        })->each(function (RelationHandler $handler) {
            $handler->apply();
        });
    }

    /**
     * @return array
     */
    protected function getRelationPolicies(): array
    {
        return [];
    }

    /**
     * @return array|string
     */
    protected function getRequiredRelations()
    {
        return [];
    }

    /**
     * @return string|null
     */
    protected function getAuthorRelation(): ?string
    {
        return null;
    }

    /**
     * @param string $name
     * @return IRelationOperator
     */
    private final function getOperator(string $name): IRelationOperator
    {
        if (!array_key_exists($name, $this->policies)) {
            throw new AuthorizationException();
        }

        $policy = $this->policies[$name];

        if ($policy instanceof IRelationOperator) {
            return $policy;
        } else {
            return $this->getOperatorFromRelation($name, $policy);
        }
    }

    /**
     * @param string $name
     * @param string $policy
     * @return IRelationOperator
     * @throws AuthorizationException
     */
    private final function getOperatorFromRelation(string $name, string $policy): IRelationOperator
    {
        if (!method_exists($this->getModel(), $name)) {
            throw new AuthorizationException();
        }

        $relation = $this->getModel()->{$name}();

        if ($relation instanceof BelongsTo) {
            return new ToOneRelationOperator($policy);
        } else if ($name instanceof BelongsToMany) {
            return new ToManyRelationOperator($policy);
        } else {
            throw new AuthorizationException();
        }
    }
}
