<?php
declare(strict_types=1);

namespace JsonApi\Requests\Traits;

use JsonApi\Requests\Params\Sorting;
use JsonApi\Requests\Params\SortingParser;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait HasSorting
 * @package App\JsonApi\Request\Traits
 */
trait HasSorting
{
    /**
     * @var Sorting
     */
    private Sorting $sorting;

    /**
     * HasSorting constructor.
     */
    protected function initializeSorting()
    {
        $this->afterValidation(function () {
            $this->sorting = SortingParser::make()->parse($this->input('sort'));
        });
    }

    /**
     * @return Sorting
     */
    public final function getSorting(): Sorting
    {
        return $this->sorting;
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public final function processSorting(Builder $builder): void
    {
        $this->sorting->process($builder);
    }
}
