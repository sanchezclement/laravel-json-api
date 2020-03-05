<?php
declare(strict_types=1);

namespace JsonApi\Requests\Relationships;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use JsonApi\Binders\JsonApiBinder;

/**
 * Class DeleteRelationshipRequest
 * @package App\JsonApi\Requests\Relationships
 */
class DeleteRelationship extends StoreRelationship
{
}
