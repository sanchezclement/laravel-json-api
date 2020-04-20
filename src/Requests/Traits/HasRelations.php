<?php
declare(strict_types=1);

namespace JsonApi\Requests\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use JsonApi\Requests\BodyRequest;
use JsonApi\Utils\Relations\Operators\IRelationOperator;
use JsonApi\Utils\Relations\Operators\ToManyRelationOperator;
use JsonApi\Utils\Relations\Operators\ToOneRelationOperator;
use JsonApi\Utils\Relations\RelationHandler;

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
    private Collection $relations;

    /**
     * @var array
     */
    private array $policies;

    public function initializeRelations(): void
    {
        $this->afterValidation(function () {
            $this->relations = collect();
            $this->policies = $this->getRelationPolicies();

            foreach ($this->input('data.relationships', []) as $name => ['data' => $data]) {
                $this->relations[$name] = new RelationHandler($name, $data, $this->getOperator($name));
            }

            if (($author = $this->getAuthorRelation())) {
                $this->getModel()->{$author}()->associate($this->user());
            }
        });
    }

    /**
     * @param string $name
     * @return Model|Collection|mixed
     */
    final public function getRelation(string $name)
    {
        return $this->relations[$name]->getRelated();
    }

    /**
     * @return Collection
     */
    final public function getToOneRelation(): Collection
    {
        return $this->relations->filter(
            fn (RelationHandler $handler) => $handler->getOperator() instanceof ToOneRelationOperator
        );
    }

    /**
     * @return Collection
     */
    final public function getToManyRelation(): Collection
    {
        return $this->relations->filter(
            fn (RelationHandler $handler) => $handler->getOperator() instanceof ToManyRelationOperator
        );
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
    final private function getOperator(string $name): IRelationOperator
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
    final private function getOperatorFromRelation(string $name, string $policy): IRelationOperator
    {
        if (!method_exists($this->getModel(), $name)) {
            throw new AuthorizationException();
        }

        $relation = $this->getModel()->{$name}();

        if ($relation instanceof BelongsTo) {
            return new ToOneRelationOperator($policy);
        } elseif ($relation instanceof BelongsToMany) {
            return new ToManyRelationOperator($policy);
        } else {
            throw new AuthorizationException();
        }
    }
}
