<?php
declare(strict_types=1);

namespace JsonApi\Requests;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use JsonApi\Requests\Traits\HasFilter;
use JsonApi\Requests\Traits\HasInclusion;
use JsonApi\Requests\Traits\HasPagination;
use JsonApi\Requests\Traits\HasSorting;
use JsonApi\Resources\ResourceCollection;

/**
 * Class FormRequest
 * @package App\JsonApi\Requests
 */
class IndexRequest extends BaseRequest
{
    use HasInclusion, HasPagination, HasSorting, HasFilter;

    /**
     * @var Collection
     */
    protected Collection $result;

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
        } else if ($builder instanceof Model) {
            return $builder::query();
        } else {
            throw new Exception("Hello world");
        }
    }

    /**
     * @param array $parameters
     * @return void
     */
    protected function process(...$parameters): void
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
