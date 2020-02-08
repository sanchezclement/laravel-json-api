<?php
declare(strict_types=1);

namespace JsonApi\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonApi\Models\JsonApiModel;
use JsonApi\Requests\Params\Pagination;

/**
 * Class RelationshipObject
 * @package App\JsonApi
 */
class RelationshipObject extends JsonResource
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @var Relation
     */
    private $relation;

    /**
     * @var string
     */
    private $relationName;

    /**
     * @var Model
     */
    private $related;

    /**
     * Create a new resource instance.
     *
     * @param $model
     * @param $relationName
     */
    public function __construct($model, $relationName)
    {
        $this->model = $model;
        $this->relationName = $relationName;
        $this->relation = $model->{$relationName}();

        $this->related = $this->model->getRelation($relationName);

        parent::__construct($model);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public final function toArray($request)
    {
        if ($this->relation instanceof HasOne || $this->relation instanceof BelongsTo) {
            return $this->serializeToOneRelationship();
        } else {
            return $this->serializeToManyRelationship();
        }
    }

    /**
     * @return array
     */
    private function serializeToOneRelationship(): array
    {
        return [
            'data' => $this->related ? ResourceIdentifier::make($this->related) : null,
            'links' => [
                'self' => route(JsonApiModel::getName($this->model) . ".id.relationships", [
                    $this->model->getKey(), $this->relation
                ]),
                'related' => $this->when($this->related, function () {
                    return route(JsonApiModel::getName($this->related) . ".id", [$this->related->getKey()]);
                })
            ],
            'meta' => [],
        ];
    }

    /**
     * @return array
     */
    private function serializeToManyRelationship(): array
    {
        $path = route(JsonApiModel::getName($this->model) . ".id.relationships", [$this->model->getKey(), $this->relationName]);

        $pagination = Pagination::make($path)->process($this->relation->getQuery());

        return [
            'data' => $this->related ? ResourceIdentifier::collection($this->related) : [],
            'links' => $pagination->getMeta(),
            'meta' => $pagination->getLinks(),
        ];
    }
}
