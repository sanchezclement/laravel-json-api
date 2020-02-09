<?php
declare(strict_types=1);

namespace JsonApi\Requests\Interfaces;

use JsonApi\Requests\Params\Inclusion;

/**
 * Interface IHasInclusion
 * @package App\JsonApi\Request\Interfaces
 */
interface IHasInclusion
{
    /**
     * @return void
     */
    function initializeInclusion(): void;

    /**
     * @return Inclusion
     */
    function getInclusions(): Inclusion;
}
