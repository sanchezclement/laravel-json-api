<?php
declare(strict_types=1);

namespace JsonApi\Controllers;

use JsonApi\Requests\Relationships\DeleteRelationship;
use JsonApi\Requests\Relationships\PatchRelationship;
use JsonApi\Requests\Relationships\StoreRelationship;
use JsonApi\Responses\NoContentResponse;

/**
 * Class RelationshipsController
 * @package JsonApi\Controllers
 */
class RelationshipsController extends JsonApiController
{
    use RelationshipApplierTrait;

    /**
     * @param StoreRelationship $request
     * @return NoContentResponse
     */
    public function store(StoreRelationship $request)
    {
        $this->storeRelationship($request);

        return $this->response()->noContent();
    }

    /**
     * @param PatchRelationship $request
     * @return NoContentResponse
     */
    public function patch(PatchRelationship $request)
    {
        $this->patchRelationship($request);

        return $this->response()->noContent();
    }

    /**
     * @param DeleteRelationship $request
     * @return NoContentResponse
     */
    public function delete(DeleteRelationship $request)
    {
        $this->deleteRelationship($request);

        return $this->response()->noContent();
    }
}
