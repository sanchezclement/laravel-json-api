<?php
declare(strict_types=1);

namespace App\JsonApi\Resources;

use App\JsonApi\Models\JsonApiModel;
use App\JsonApi\Requests\Params\Pagination;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class RelationshipObject
 * @package App\JsonApi
 */
class RelationshipObject extends JsonResource
{
    /**
     * @var JsonApiModel
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
     * @var JsonApiModel
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
                'self' => route("{$this->model->getName()}.id.relationships", [
                    $this->model->getKey(), $this->relation
                ]),
                'related' => $this->when($this->related, function () {
                    return route("{$this->related->getName()}.id", [$this->related->getKey()]);
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
        $path = route("{$this->model->getName()}.id.relationships", [$this->model->getKey(), $this->relationName]);

        $pagination = Pagination::make($path)->process($this->relation->getQuery());

        return [
            'data' => $this->related ? ResourceIdentifier::collection($this->related) : [],
            'links' => $pagination->getMeta(),
            'meta' => $pagination->getLinks(),
        ];
    }
}
