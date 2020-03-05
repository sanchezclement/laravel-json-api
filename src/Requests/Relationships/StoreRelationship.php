<?php
declare(strict_types=1);

namespace JsonApi\Requests\Relationships;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Class StoreRelationshipRequest
 * @package App\JsonApi\Requests\Relationships
 */
class StoreRelationship extends RelationshipRequest
{
    /**
     * @inheritDoc
     */
    final protected function validateRelation(Relation $relation)
    {
        if ($relation instanceof BelongsToMany) {
            $this->prepareRules();
        } else {
            $this->methodNotAllowed();
        }
    }

    final private function prepareRules(): void
    {
        $this->rules([
            'data' => 'required|array|nullable',
            'data.*' => 'required_with:data|array',
            'data.*.type' => 'required_with:data.*|string',
            'data.*.id' => 'required_with:data|.*|string',
        ]);
    }
}
