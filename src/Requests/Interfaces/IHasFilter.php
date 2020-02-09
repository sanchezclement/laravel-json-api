<?php
declare(strict_types=1);

namespace JsonApi\Requests\Interfaces;

use JsonApi\Requests\Params\Filter;

/**
 * Interface IHasFilter
 * @package App\JsonApi\Request\Interfaces
 */
interface IHasFilter
{
    function initializeFilter(): void;

    /**
     * @return Filter
     */
    public function getFilter(): Filter;
}
