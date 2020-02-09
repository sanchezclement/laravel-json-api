<?php
declare(strict_types=1);

namespace JsonApi\Requests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use JsonApi\Requests\Params\Filter;
use JsonApi\Requests\Params\Pagination;
use JsonApi\Requests\Params\Sorting;

/**
 * Class RequestBuilder
 * @package JsonApi\Requests
 */
class RequestBuilder
{
    /**
     * @var Pagination
     */
    private Pagination $pagination;

    /**
     * @var Filter
     */
    private Filter $filter;

    /**
     * @var Sorting
     */
    private Sorting $sorting;

    /**
     * RequestBuilder constructor.
     * @param Pagination $pagination
     * @param Filter $filter
     * @param Sorting $sorting
     */
    public function __construct(Filter $filter, Pagination $pagination, Sorting $sorting)
    {
        $this->pagination = $pagination;
        $this->filter = $filter;
        $this->sorting = $sorting;
    }

    /**
     * @param Filter $filter
     * @param Pagination $pagination
     * @param Sorting $sorting
     * @return static
     */
    public static function make(Filter $filter, Pagination $pagination, Sorting $sorting)
    {
        return new static($filter, $pagination, $sorting);
    }

    /**
     * @param null $builder
     * @return Builder
     */
    public function build($builder = null): Builder
    {
        $builder = $this->getBuilder($builder);

        $this->filter->process($builder);
        $this->pagination->process($builder);
        $this->sorting->process($builder);

        return $builder;
    }

    /**
     * @param null $builder
     * @return Builder|null
     */
    private function getBuilder($builder)
    {
        if ($builder instanceof Relation) {
            return $builder->getQuery();
        } else if ($builder instanceof Model) {
            return $builder::query();
        } else {
            return $builder;
        }
    }
}
