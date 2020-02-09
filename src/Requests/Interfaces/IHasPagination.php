<?php
declare(strict_types=1);

namespace JsonApi\Requests\Interfaces;

use JsonApi\Requests\Params\Pagination;

/**
 * Interface IHasPagination
 * @package App\JsonApi\Request\Interfaces
 */
interface IHasPagination
{
    function initializePagination(): void;

    /**
     * @return Pagination
     */
    function getPagination(): Pagination;
}
