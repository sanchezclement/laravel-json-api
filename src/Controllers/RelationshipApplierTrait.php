<?php
declare(strict_types=1);

namespace JsonApi\Controllers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use JsonApi\Requests\Relationships\DeleteRelationship;
use JsonApi\Requests\Relationships\PatchRelationship;
use JsonApi\Requests\Relationships\StoreRelationship;

/**
 * Trait RelationshipApplierTrait
 * @package JsonApi\Controllers
 */
trait RelationshipApplierTrait
{
    /**
     * @param StoreRelationship $request
     */
    final public function storeRelationship(StoreRelationship $request): void
    {
        $request->getRelation()->syncWithoutDetaching($request->getRelated());
    }

    /**
     * @param PatchRelationship $request
     */
    final public function patchRelationship(PatchRelationship $request): void
    {
        $relation = $request->getRelation();
        $related = $request->getRelated();

        if ($relation instanceof BelongsTo) {
            if (is_null($related)) {
                $relation->dissociate()->save();
            } else {
                $relation->associate($related)->save();
            }
        } else {
            $relation->sync($related);
        }
    }

    /**
     * @param DeleteRelationship $request
     */
    final public function deleteRelationship(DeleteRelationship $request): void
    {
        $request->getRelation()->detach($request->getRelated());
    }
}
