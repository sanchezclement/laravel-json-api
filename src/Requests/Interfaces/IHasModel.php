<?php
declare(strict_types=1);

namespace JsonApi\Requests\Interfaces;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface IHasModel
 * @package App\JsonApi\Request\Interfaces
 */
interface IHasModel
{
    function initializeModel(): void;

    /**
     * @return Model
     */
    function getModel(): Model;
}
