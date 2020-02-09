<?php
declare(strict_types=1);

namespace JsonApi\Requests\Traits;

use JsonApi\Requests\Params\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait HasFilter
 * @package App\JsonApi\Request\Traits
 */
trait HasFilter
{
    /**
     * @var Filter
     */
    private Filter $filter;

    /**
     * HasFilter constructor.
     */
    protected function initializeFilter(): void
    {
        $this->rules(['filter' => 'array',]);

        $this->afterValidation(function () {
            $this->filter = Filter::make($this->input('filter', []));
        });
    }

    /**
     * @return Filter
     */
    public final function getFilter(): Filter
    {
        return $this->filter;
    }

    /**
     * @param Builder $builder
     */
    public final function processFilter(Builder $builder)
    {
        $this->filter->process($builder);
    }
}
