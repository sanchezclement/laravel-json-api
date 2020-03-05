<?php
declare(strict_types=1);

namespace JsonApi\Requests\Relationships;

use Illuminate\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use JsonApi\Binders\JsonApiBinder;
use JsonApi\Requests\ResourceRequest;

/**
 * Class BaseRequest
 * @package App\JsonApi\Requests\Relationships
 */
abstract class RelationshipRequest extends ResourceRequest
{
    /**
     * @var BelongsTo|BelongsToMany
     */
    private BelongsTo $relation;

    /**
     * @var Collection|Model
     */
    private $related;

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

        $this->beforeValidation(fn () => $this->initializeValidation());
        $this->afterValidation(fn () => $this->finalizeValidation());
    }

    final private function initializeValidation(): void
    {
        $model = $this->getModel();
        $relationName = $this->route('relationship');

        if (!method_exists($model, $relationName)) {
            $this->relationshipNotFound();
        }

        $this->relation = $model->{$relationName}();

        if (!($this->relation instanceof Relation)) {
            $this->relationshipNotFound();
        }

        $this->validateRelation($this->relation);
    }

    /**
     * @param Relation $relation
     * @return mixed
     */
    abstract protected function validateRelation(Relation $relation);

    /**
     * @return BelongsTo|BelongsToMany
     */
    final public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @return Collection|Model
     */
    final public function getRelated()
    {
        return $this->related;
    }

    final private function finalizeValidation()
    {
        $this->related = JsonApiBinder::get()->findOrNull($this->validated());

        if (is_null($this->related)) {
            $this->relatedNotFound();
        }

        $this->authorizeRelated();
    }

    final private function authorizeRelated(): void
    {
        $gate = app(Gate::class);

        if ($this->related instanceof Collection) {
            $this->related->each(fn (Model $model) => $gate->authorize('update', $model));
        } else {
            $gate->authorize('update', $this->getRelated());
        }
    }

    final protected function relatedNotFound()
    {
        abort(422, "One of the related models have not been found.");
    }


    final protected function methodNotAllowed()
    {
        abort(405, "This method is not allowed");
    }

    final protected function relationshipNotFound()
    {
        abort(404, "Relationship not found");
    }
}
