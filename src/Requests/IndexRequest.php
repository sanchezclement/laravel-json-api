<?php
declare(strict_types=1);

namespace JsonApi\Requests;

use JsonApi\Models\JsonApiModel;
use JsonApi\Requests\Traits\HasFilter;
use JsonApi\Requests\Traits\HasPagination;
use JsonApi\Requests\Traits\HasSorting;
use JsonApi\Resources\ResourceCollection;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

/**
 * Class FormRequest
 * @package App\JsonApi\Requests
 */
class IndexRequest extends BaseRequest
{
    use HasPagination, HasSorting, HasFilter {
        HasPagination::__construct as __constructHasPagination;
        HasSorting::__construct as __constructHasSorting;
        HasFilter::__construct as __constructHasFilter;
    }

    /**
     * @var Collection
     */
    protected $result;

    /**
     * @param array $query The GET parameters
     * @param array $request The POST parameters
     * @param array $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array $cookies The COOKIE parameters
     * @param array $files The FILES parameters
     * @param array $server The SERVER parameters
     * @param string|resource|null $content The raw body data
     */
    public function __construct(
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        $this->__constructHasPagination();
        $this->__constructHasSorting();
        $this->__constructHasFilter();
    }

    /**
     * @return mixed
     */
    public function makeResource()
    {
        return ResourceCollection::makeFromCollection($this->result, $this->getPagination(), $this->getInclusions());
    }

    /**
     * @param null $builder
     * @return Builder
     * @throws Exception
     */
    public function processBuilder($builder = null): Builder
    {
        $builder = $this->getBuilder($builder);

        $this->processFilter($builder);
        $this->processSorting($builder);
        $this->processPagination($builder);

        return $builder;
    }

    /**
     * @param null $builder
     * @return Builder|null
     * @throws Exception
     */
    public function getBuilder($builder = null)
    {
        if (is_null($builder)) {
            return $this->getModel()->query();
        } else if ($builder instanceof Builder) {
            return $builder;
        } else if ($builder instanceof Relation) {
            return $builder->getQuery();
        } else if ($builder instanceof JsonApiModel) {
            return $builder::query();
        } else {
            throw new Exception("Hello world");
        }
    }

    /**
     * @param array $parameters
     * @return void
     */
    protected function defaultAction(...$parameters): void
    {
        $this->result = $this->processBuilder($parameters[0] ?? null)->get();
    }

    /**
     * @return string
     */
    protected function getPolicy(): string
    {
        return 'index';
    }
}
