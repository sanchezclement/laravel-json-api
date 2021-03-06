<?php
declare(strict_types=1);

namespace JsonApi\Requests;

use Illuminate\Support\Collection;
use JsonApi\Requests\Interfaces\IHasFilter;
use JsonApi\Requests\Interfaces\IHasPagination;
use JsonApi\Requests\Interfaces\IHasSorting;
use JsonApi\Requests\Traits\HasFilter;
use JsonApi\Requests\Traits\HasPagination;
use JsonApi\Requests\Traits\HasSorting;

/**
 * Class FormRequest
 * @package App\JsonApi\Requests
 */
class IndexRequest extends ResourceRequest implements IHasPagination, IHasSorting, IHasFilter
{
    use HasPagination, HasSorting, HasFilter;

    /**
     * @var Collection
     */
    protected Collection $result;

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

        $this->initializePagination();
        $this->initializeSorting();
        $this->initializeFilter();
    }

    /**
     * @return string
     */
    protected function getPolicy(): string
    {
        return 'index';
    }
}
