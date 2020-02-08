<?php
declare(strict_types=1);

namespace JsonApi\Requests\Params;

/**
 * Class RelationPagination
 * @package App\JsonApi\Request\Params
 */
class RelationPagination
{
    /**
     * @var int
     */
    const DEFAULT_PAGE_SIZE = 5;

    /**
     * @var int
     */
    const MAX_PAGE_SIZE = 5;
}
