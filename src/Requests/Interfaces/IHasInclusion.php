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
    public function initializeInclusion(): void;

    /**
     * @return Inclusion
     */
    public function getInclusions(): Inclusion;
}
