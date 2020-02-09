<?php
declare(strict_types=1);

namespace JsonApi\Requests\Interfaces;

use Illuminate\Foundation\Http\FormRequest;
use JsonApi\Requests\Params\Inclusion;

/**
 * Interface IHasRelations
 * @package App\JsonApi\Request\Interfaces
 */
interface IHasRelations
{
    function initializeRelations(): void;
}
