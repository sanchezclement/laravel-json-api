<?php
declare(strict_types=1);

namespace JsonApi\Requests\Relationships;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Class PatchRelationshipRequest
 * @package App\JsonApi\Requests\Relationships
 */
class PatchRelationship extends RelationshipRequest
{
    /**
     * @inheritDoc
     */
    final protected function validateRelation(Relation $relation)
    {
        if ($relation instanceof BelongsTo) {
            $this->prepareToOneRules();
        } elseif ($relation instanceof BelongsToMany) {
            $this->prepareToManyRules();
        } else {
            $this->methodNotAllowed();
        }
    }

    final private function prepareToOneRules(): void
    {
        $this->rules([
            'data' => 'required|array|nullable',
            'data.type' => 'required_with:data|string',
            'data.id' => 'required_with:data|string',
        ]);
    }

    final private function prepareToManyRules(): void
    {
        $this->rules([
            'data' => 'required|array|nullable',
            'data.*' => 'required_with:data|array',
            'data.*.type' => 'required_with:data.*|string',
            'data.*.id' => 'required_with:data|.*|string',
        ]);
    }
}
