<?php
declare(strict_types=1);

namespace App\JsonApi\Requests\Traits;

use App\JsonApi\Requests\Params\Pagination;
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
    private $pagination;

    /**
     * HasPagination constructor.
     */
    public function __construct()
    {
        $this->afterValidation(function () {
            $this->pagination = Pagination::make(
                $this->path(), $this->input('page.number'), $this->input('page.size')
            );
        });

        $this->addRules([
            'page.number' => 'integer|min:0',
            'page.size' => 'integer|min:1|max:50',
        ]);
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
