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
        $this->rules(['page.number' => 'integer|min:0', 'page.size' => 'integer|min:1|max:500',]);

        $this->afterValidation(function () {
            $data = $this->validated()['page'] ?? [];

            $pageNumber = array_key_exists('number', $data) ? intval($data['number']) : null;
            $pageSize = array_key_exists('size', $data) ? intval($data['size']) : null;

            $this->pagination = Pagination::make($this->path(), $pageNumber, $pageSize);
        });
    }

    /**
     * @return Pagination
     */
    final public function getPagination(): Pagination
    {
        return $this->pagination;
    }

    /**
     * @param Builder $builder
     */
    final public function processPagination(Builder $builder)
    {
        $this->pagination->process($builder);
    }
}
