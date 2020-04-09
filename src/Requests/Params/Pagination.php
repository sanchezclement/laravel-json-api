<?php
declare(strict_types=1);

namespace JsonApi\Requests\Params;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class Pagination
 * @package App\JsonApi\Request\Params
 */
class Pagination
{
    /**
     * @var int
     */
    const DEFAULT_PAGE_SIZE = 50;

    /**
     * @var int
     */
    const DEFAULT_PAGE_NUMBER = 1;

    /**
     * @var int
     */
    const MIN_PAGE_SIZE = 1;

    /**
     * @var int
     */
    const MAX_PAGE_SIZE = 50;

    /**
     * @var int
     */
    const MIN_PAGE_NUMBER = 1;

    /**
     * @var string
     */
    private $path;

    /**
     * @var int
     */
    private $pageNumber;

    /**
     * @var int
     */
    private $pageSize;

    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $totalSize;

    /**
     * @var int
     */
    private $totalPage;

    /**
     * Create a new resource instance.
     * @param string $path
     * @param int|null $pageNumber
     * @param int|null $pageSize
     */
    public function __construct(string $path, ?int $pageNumber = null, ?int $pageSize = null)
    {
        $this->path = $path;
        $this->pageNumber = $pageNumber ?? static::DEFAULT_PAGE_NUMBER;
        $this->pageSize = $pageSize ?? static::DEFAULT_PAGE_SIZE;
        $this->size = 0;
        $this->totalSize = 0;
    }

    /**
     * @param string $path
     * @param int|null $pageNumber
     * @param int|null $pageSize
     * @return static
     */
    public static function make(string $path, ?int $pageNumber = null, ?int $pageSize = null): self
    {
        return new static($path, $pageNumber, $pageSize);
    }

    /**
     * @param Builder $builder
     * @return Pagination
     */
    public function process(Builder $builder): self
    {
        $this->totalSize = $builder->count();
        $this->totalPage = intval($this->totalSize / $this->pageSize) + 1;

        $builder->limit($this->pageSize);

        if ($this->pageNumber > 1) {
            $builder->offset(($this->pageNumber - 1) * $this->pageSize);
        }

        $this->size = $builder->count();

        return $this;
    }

    /**
     * @return array
     */
    public function getMeta(): array
    {
        return [
            'pageNumber' => $this->pageNumber,
            'pageSize' => $this->pageSize,
            'size' =>  $this->size,
            'totalSize' => $this->totalSize,
            'totalPage' => $this->totalPage,
        ];
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return [
            'first' => $this->generatePageLink(static::MIN_PAGE_NUMBER),
            'prev' => $this->generatePageLink($this->pageNumber - 1),
            'self' => $this->generatePageLink($this->pageNumber),
            'next' => $this->generatePageLink($this->pageNumber + 1),
            'last' => $this->generatePageLink($this->totalPage),
        ];
    }

    /**
     * @param int $pageNumber
     * @return string
     */
    private function generatePageLink(int $pageNumber): ?string
    {
        $url = url($this->path);

        if ($pageNumber < static::MIN_PAGE_NUMBER || $pageNumber > $this->totalPage) {
            return null;
        }

        $query = [];

        if ($pageNumber !== static::DEFAULT_PAGE_NUMBER) {
            $query['page[number]'] = $pageNumber;
        }

        if ($this->pageSize !== static::DEFAULT_PAGE_SIZE) {
            $query['page[size]'] = $this->pageSize;
        }

        if ($query) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }
}
