<?php
declare(strict_types=1);

namespace JsonApi\Requests\Traits;

use JsonApi\Requests\Params\Pagination;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait HasPagination
 * @package App\JsonApi\Request\Traits
 */
trait HasPagination
{
    /**
     * @var Pagination
     */
    private Pagination $pagination;

    /**
     * HasPagination constructor.
     */
    public function initializePagination(): void
    {
        $this->rules(['page.number' => 'integer|min:0', 'page.size' => 'integer|min:1|max:50',]);

        $this->afterValidation(function () {
            $this->pagination = Pagination::make(
                $this->path(), $this->input('page.number'), $this->input('page.size')
            );
        });
    }

    /**
     * @return Pagination
     */
    public final function getPagination(): Pagination
    {
        return $this->pagination;
    }

    /**
     * @param Builder $builder
     */
    public final function processPagination(Builder $builder)
    {
        $this->pagination->process($builder);
    }
}
