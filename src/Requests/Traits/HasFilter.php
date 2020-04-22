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
    public function initializeFilter(): void
    {
        $this->rules(['filter' => 'array',]);

        $this->afterValidation(function () {
            $this->filter = Filter::make($this->input('filter', []));
        });
    }

    /**
     * @return Filter
     */
    final public function getFilter(): Filter
    {
        return $this->filter;
    }

    /**
     * @param Builder $builder
     */
    final public function processFilter(Builder $builder)
    {
        $this->filter->process($builder);
    }
}
