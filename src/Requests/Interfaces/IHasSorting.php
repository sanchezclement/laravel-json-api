<?php
declare(strict_types=1);

namespace JsonApi\Requests\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use JsonApi\Requests\Params\Pagination;
use JsonApi\Requests\Params\Sorting;

/**
 * Interface IHasSorting
 * @package App\JsonApi\Request\Interfaces
 */
interface IHasSorting
{
    function initializeSorting(): void;

    /**
     * @return Sorting
     */
    function getSorting(): Sorting;
}
