<?php
declare(strict_types=1);

namespace JsonApi\Requests\Traits;

/**
 * Trait HasBody
 * @package App\JsonApi\Request\Traits
 */
trait HasBody
{
    use HasAttributes, HasRelations;
}
