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
    private $sorting;

    /**
     * HasSorting constructor.
     */
    public function __construct()
    {
        $this->afterValidation(function () {
            $this->initializeSorting();
        });
    }

    private final function initializeSorting(): void
    {
        $this->sorting = SortingParser::make()->parse($this->input('sort'));
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
