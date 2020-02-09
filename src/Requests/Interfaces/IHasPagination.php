<?php
declare(strict_types=1);

namespace JsonApi\Requests\Interfaces;

use Illuminate\Foundation\Http\FormRequest;
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
    public function getPagination(): Pagination;
}
